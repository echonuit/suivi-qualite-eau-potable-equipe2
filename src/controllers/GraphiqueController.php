<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use DateTimeImmutable;
use EauPotable\models\GraphiqueModel;
use EauPotable\models\ReseauModel;
use EauPotable\views\GraphiqueView;
use EauPotable\views\Layout;

final class GraphiqueController
{
    public function executer(array $requete): string
    {
        /**
     * @param array<string, mixed> $requete
     * @return string
     */
        $codeReseau = $requete['reseau'] ?? '';
        $codeCommune = $requete['commune'] ?? '';

        $reseauModele = new ReseauModel();
        $graphiqueModele = new GraphiqueModel();
        $derniereDate = $graphiqueModele->getDateDernierePrelevement($codeReseau);
        $commune = $reseauModele->getCommuneParCodeInsee($codeCommune) ?? [];
        $reseaux = $reseauModele->getReseauxLieesAuCommune($codeCommune);
        $reseauSelectionne = [];
        foreach ($reseaux as $r) {
            if ($r['code_reseau'] === $codeReseau) {
                $reseauSelectionne = $r;
                break;
            }
        }
        if ($derniereDate === null) {
            return (new GraphiqueView())->afficher($commune, $reseauSelectionne, []);
        }

        $fin = new DateTimeImmutable($derniereDate);
        $debut = $fin->modify('-12 months');

        $mesures = $graphiqueModele->getMesuresParMois(
            $codeReseau,
            $debut->format('Y-m-d'),
            $fin->format('Y-m-d')
        );

        return (new GraphiqueView())->afficher(
            $commune,
            $reseauSelectionne,
            $mesures
        );
    }

    public function executerPage(array $requete): string
    {
        $contenu = $this->executer($requete);

        return (new Layout(
            'Graphique',
            'Évolution des mesures du réseau',
            $contenu
        ))->afficher();
    }
}
