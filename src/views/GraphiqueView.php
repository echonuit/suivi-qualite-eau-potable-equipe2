<?php

declare(strict_types=1);

namespace EauPotable\views;

use DateTimeImmutable;

final class GraphiqueView
{
    public function afficher(array $mesures, int $limite): string
    {
        $donnees = $this->completerDate($mesures);

        ob_start();
        ?>
        <section class="graphique-reseau">
            <?php if ($donnees === [] || array_sum(array_column($donnees, 'valeur')) === 0):
                print_r($donnees)?>
                <p>Aucune mesure.</p>
            <?php else: ?>
                <?= $this->dessinerGraphique($donnees,$limite) ?>
            <?php endif; ?>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    // Si le mois n'a pas de prélèvement
    private function completerDate(array $mesures): array
    {
        if ($mesures === []) {
            return [];
        }

        $parJour = [];
        foreach ($mesures as $ligne) {
            $parJour[$ligne['date']] = $ligne['valeur'];
        }

        $premiereDate = new DateTimeImmutable(array_key_first($parJour));
        $derniereDate = new DateTimeImmutable(array_key_last($parJour));

        $donnees = [];

        while ($premiereDate <= $derniereDate) {
            $date = $premiereDate->format('Y-m-d');

            $donnees[] = [
                'date' => $date,
                'valeur' => $parJour[$date] ?? 0,
            ];

            $premiereDate = $premiereDate->modify('+1 day');
        }

        return $donnees;
    }

    // Construit le graphique SVG avec les valeurs récupérées dans la base.
    private function dessinerGraphique(array $donnees,int $limite): string
    {
        $largeur = 800;
        $hauteur = 360;
        $gauche = 55;
        $droite = $largeur - 25;
        $haut = 25;
        $bas = $hauteur - 65;

        $maximum = max(array_column($donnees, 'valeur'));
        $maximum = max(1, (int) ceil($maximum * 1.1));
        $maximum = max($maximum,$limite);

        $nombrePoints = count($donnees);
        $ecartX = ($droite - $gauche) / max(1, $nombrePoints - 1);

        $points = [];
        foreach ($donnees as $index => $ligne) {
            if($ligne['valeur'] > 0){
                $x = $gauche + ($index * $ecartX);
                $y = $bas - (($ligne['valeur'] / $maximum) * ($bas - $haut));

                $points[] = [
                    'x' => $x,
                    'y' => $y,
                    'date' => $ligne['date'],
                    'valeur' => $ligne['valeur'],
                ];
            }
        }

        $coordonnees = [];
        foreach ($points as $point) {
            $coordonnees[] = round($point['x'], 1) . ',' . round($point['y'], 1);
        }

        ob_start();
        ?>
        <figure class="carte-graphique">
            <svg
                viewBox="0 0 <?= $largeur ?> <?= $hauteur ?>"
                role="img"
                aria-label="Nombre de prélèvements par mois "
                style="width: 100%; height: auto; font-family: Arial, sans-serif;"
            >
                <!-- Les axes -->
                <line x1="<?= $gauche ?>" y1="<?= $bas ?>"
                      x2="<?= $droite ?>" y2="<?= $bas ?>"
                      stroke="#000" />
                <line x1="<?= $gauche ?>" y1="<?= $haut ?>"
                      x2="<?= $gauche ?>" y2="<?= $bas ?>"
                      stroke="#000" />

                <!-- Graduations -->
                <?php for ($i = 0; $i <= 4; $i++):
                    $valeur = round($maximum * $i / 4);
                    $y = $bas - (($valeur / $maximum) * ($bas - $haut));
                ?>
                    <line x1="<?= $gauche ?>" y1="<?= $y ?>"
                          x2="<?= $droite ?>" y2="<?= $y ?>"
                          stroke="#dddddd" />
                    <text x="<?= $gauche - 10 ?>" y="<?= $y + 4 ?>"
                          text-anchor="end" font-size="12" fill="#333">
                        <?= $valeur ?>
                    </text>
                <?php endfor; ?>

                <!-- Courbe -->
                <polyline
                    points="<?= implode(' ', $coordonnees) ?>"
                    fill="none"
                    stroke="#0000FF"
                    stroke-width="3"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                />

                <!-- Points et noms des mois -->
                <?php foreach ($points as $point): ?>
                    <circle cx="<?= round($point['x'], 1) ?>"
                            cy="<?= round($point['y'], 1) ?>"
                            r="4" fill="#0000FF" />
                    <text x="<?= round($point['x'], 1) ?>"
                          y="<?= $bas + 20 ?>"
                          transform="rotate(90 ,<?= round($point['x'], 1)?>,<?=$bas + 20?>)"
                          text-anchor="middle" font-size="11" fill="#000">
                        <?= $this->nomMois($point['date']) ?>
                    </text>
                <?php endforeach; ?>

                <!-- Courbe de limite -->
                <line x1="<?= $gauche ?>" y1="<?= $bas - (($limite / $maximum) * ($bas - $haut)) ?>"
                      x2="<?= $droite ?>" y2="<?= $bas - (($limite / $maximum) * ($bas - $haut))  ?>"
                      stroke="#ff0000" />
            </svg>
            <figcaption>Valeur du paramètre à chaque prélèvement</figcaption>
        </figure>
        <?php

        return (string) ob_get_clean();
    }

    // Transforme 2026-01 en Jan. 2026.
    private function nomMois(string $mois): string
    {
        $date = new DateTimeImmutable($mois . '-01');
        $noms = [
            1 => 'Jan.', 2 => 'Fév.', 3 => 'Mars', 4 => 'Avr.',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil.', 8 => 'Août',
            9 => 'Sept.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Déc.',
        ];

        return $noms[(int) $date->format('n')] . ' ' . $date->format('y');
    }
}
