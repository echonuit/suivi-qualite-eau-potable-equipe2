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


        // on récupère le parametre recherché et le réseau et commune sélectionnés par l'utilisateur dans l'url
        $codeCommuneSelection = trim((string) ($requete['commune'] ?? ''));
        $codeReseauSelection = trim((string) ($requete['reseau'] ?? ''));

        $derniereMesure = $parametresModele->getDerniereMesureLieeAuReseau($codeReseauSelection);
        $nombreMesures = $parametresModele->getNombreDesMesuresLieesAuReseau($codeReseauSelection);

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

        // deuxième argument possède valeur par défaut de'' (string $rechercheParam = ''), donc il reste simplement vide lors de cet appel...
        $parametres = $parametresModele->getParametresLiesAuReseau($codeReseauSelection);

        //si aucun prélèvement est associé au réseau selectionné
        if (empty($parametres)) { //càd $parametres === []
            return $vue->afficherAucunParametre($reseauSelectionne);
        }

        $codesParamDisponibles = [];

        // on vérifie que le paramètre est disponible sur le réseau,
        // et on crée un tableau de paires clé-valeur, comme 'code' => true,
        // pour vérifier ultérieurement avec isset().
        foreach ($parametres as $parametre) {
            $codesParamDisponibles[$parametre['code_param_sandre']] = true;
        }

        $parametresLiesBruts = $requete['parametres'] ?? '';

        if (is_string($parametresLiesBruts)) {
            // format d'url parametres=1340,1302...
            $codesSelectionnes = $parametresLiesBruts === '' ? [] : explode(',', $parametresLiesBruts);
        } elseif (is_array($parametresLiesBruts)) {
            $codesSelectionnes = $parametresLiesBruts;
        } else {
            $codesSelectionnes = [];
        }

        $selectionDeParametres = [];

        foreach ($codesSelectionnes as $codeParam) {
            if (is_string($codeParam) && isset($codesParamDisponibles[$codeParam])) {
                $selectionDeParametres[$codeParam] = true;
            }
        }

        $parametresSelectionnes = array_keys($selectionDeParametres);

        return $vue-> afficherParametres($communeSelectionnee, $reseauSelectionne, $parametres, $parametresSelectionnes, $nombreMesures, $derniereMesure);
    }
}
