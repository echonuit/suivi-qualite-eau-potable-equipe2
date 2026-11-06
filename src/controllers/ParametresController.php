<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\models\ParametresModel;
use EauPotable\models\ReseauModel;
use EauPotable\views\ParametresView;

final class ParametresController
{
    /**
    * @param array<string, mixed> $requete
    * @return string
    */
    public function executer(array $requete): string
    {

        $parametresModele = new ParametresModel();
        $reseauModele = new ReseauModel();
        $vue = new ParametresView();

        // on récupère le réseau et commune sélectionnés par l'utilisateur dans l'url
        $codeReseauSelection = trim((string) ($requete['reseau'] ?? ''));
        $codeCommuneSelection = trim((string) ($requete['commune'] ?? ''));

        if ($codeReseauSelection === '' || $codeCommuneSelection === '') {
            http_response_code(400);
            return 'Aucun réseau ou commune sélectionné.';
        }

        $communeSelectionnee = $reseauModele->getCommuneParCodeInsee($codeCommuneSelection);

        if ($communeSelectionnee === null) {
            http_response_code(404);
            return 'Commune non trouvée.';
        }

        $reseaux = $reseauModele->getReseauxLieesAuCommune($codeCommuneSelection);

        $reseauSelectionne = null;

        foreach ($reseaux as $reseau) {
            if ($reseau['code_reseau'] === $codeReseauSelection) {
                $reseauSelectionne = $reseau;
                break;
            }
        }

        if ($reseauSelectionne === null) {
            http_response_code(404);
            return 'Réseau non trouvé pour la commune selectionnée.';
        }

        $parametres = $parametresModele->getParametresLiesAuReseau($codeReseauSelection);

        //si aucun prélèvement est associé au réseau selectionné
        if (empty($parametres)) { //càd $parametres === []
            return $vue->afficherAucunParametre($reseauSelectionne);
        }

        // Temporaire car: duplication de partie layout de ces methodes
        return $vue->afficherRechercheParametres($parametres) . $vue-> afficherParametres($communeSelectionnee, $reseauSelectionne, $parametres);
    }
}
