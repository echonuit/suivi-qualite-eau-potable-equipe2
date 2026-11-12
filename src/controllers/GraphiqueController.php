<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use DateTimeImmutable;
use EauPotable\models\GraphiqueModel;
use EauPotable\models\ReseauModel;
use EauPotable\views\GraphiqueView;

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
            return (new GraphiqueView())->afficher([],0);
        }

        $fin = new DateTimeImmutable($derniereDate);
        $debut = $fin->modify('-12 months');

        $mesures = $graphiqueModele->getMesures(
            $codeReseau,
            "1340",
            $debut->format('Y-m-d'),
            $fin->format('Y-m-d')
        );

        return (new GraphiqueView())->afficher($mesures,$graphiqueModele->getLimite("1340"));
    }

}
