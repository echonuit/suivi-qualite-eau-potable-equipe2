<?php

declare(strict_types=1);

namespace EauPotable\views;

use DateTimeImmutable;

final class GraphiqueView
{
    public function afficher(array $commune, array $reseau, array $mesures): string
    {
        $donnees = $this->completerMois($mesures);

        ob_start();
        ?>
        <section class="graphique-reseau">
            <h2>Mesures par mois</h2>

            <?php if ($donnees === [] || array_sum(array_column($donnees, 'nombre')) === 0): ?>
                <p>Aucune mesure.</p>
            <?php else: ?>
                <?= $this->dessinerGraphique($donnees) ?>
            <?php endif; ?>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    // Si le mois n'a pas de prélèvement
    private function completerMois(array $mesures): array
    {
        if ($mesures === []) {
            return [];
        }

        $parMois = [];
        foreach ($mesures as $ligne) {
            $parMois[$ligne['mois']] = $ligne['nombre'];
        }

        $premierMois = new DateTimeImmutable(array_key_first($parMois) . '-01');
        $dernierMois = new DateTimeImmutable(array_key_last($parMois) . '-01');

        $donnees = [];

        while ($premierMois <= $dernierMois) {
            $mois = $premierMois->format('Y-m');

            $donnees[] = [
                'mois' => $mois,
                'nombre' => $parMois[$mois] ?? 0,
            ];

            $premierMois = $premierMois->modify('+1 month');
        }

        return $donnees;
    }

    // Construit le graphique SVG avec les valeurs récupérées dans la base.
    private function dessinerGraphique(array $donnees): string
    {
        $largeur = 800;
        $hauteur = 360;
        $gauche = 55;
        $droite = $largeur - 25;
        $haut = 25;
        $bas = $hauteur - 65;

        $maximum = max(array_column($donnees, 'nombre'));
        $maximum = max(1, (int) ceil($maximum * 1.1));

        $nombrePoints = count($donnees);
        $ecartX = ($droite - $gauche) / max(1, $nombrePoints - 1);

        $points = [];
        foreach ($donnees as $index => $ligne) {
            $x = $gauche + ($index * $ecartX);
            $y = $bas - (($ligne['nombre'] / $maximum) * ($bas - $haut));

            $points[] = [
                'x' => $x,
                'y' => $y,
                'mois' => $ligne['mois'],
                'nombre' => $ligne['nombre'],
            ];
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
                          text-anchor="middle" font-size="11" fill="#000">
                        <?= $this->nomMois($point['mois']) ?>
                    </text>
                <?php endforeach; ?>
            </svg>
            <figcaption>Nombre de prélèvements par mois</figcaption>
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
