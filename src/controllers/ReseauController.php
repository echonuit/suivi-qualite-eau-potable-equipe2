<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\models\ReseauModel;
use EauPotable\views\ReseauView;

final class ReseauController
{
    /**
    * @param array<string, mixed> $requete
    * @return string
    */
    public function executer(array $requete): string
    {
        $modele = new ReseauModel();
        $vue = new ReseauView();

        $communes = $modele->getToutesLesCommunes();

        $selectionCommune = trim((string) ($requete['commune'] ?? ''));
        $communeSelectionnee = null;

        foreach ($communes as $commune) {

            if ($selectionCommune === $commune['insee']) {
                $communeSelectionnee = $commune;
                break;
            }
        }

        // si l'utilisateur n'a pas sélectionné la commune : on affiche le choix des communes
        if ($communeSelectionnee === null) {
            return $vue->afficherRechercheCommune($communes);
        }

        $reseaux = $modele->getReseauxLieesAuCommune($communeSelectionnee['insee']);

        // si aucun réseau est lié à la commune sélectionnée
        if (empty($reseaux)) { //count($reseaux) === 0
            return $vue->afficherAucunReseau($communeSelectionnee);
        }

        // on récupère le réseau sélectionné par l'utilisateur
        $selectionReseau = trim((string) ($requete['reseau'] ?? ''));

        // si un seul réseau est lié à la commune sélectionnée --- selection directe automatique
        if (count($reseaux) === 1) {
            $reseauSelectionne  = $reseaux[0];
        } else { //si plusieurs reseaux disponibles pour la commune selectionee

            if ($selectionReseau === '') {//
                return $vue->afficherOptionsReseaux($communeSelectionnee, $reseaux);

            }

            // valider que le reseau selectioné correspond à la commune souhaitée
            $reseauSelectionne = null;

            foreach ($reseaux as $reseau) {
                if ($selectionReseau === $reseau['code_reseau']) {
                    $reseauSelectionne = $reseau;
                    break;
                }
            }

            // si reseau pas encore selectionné ou pas valide: on affiche le choix de réseau
            if ($reseauSelectionne === null) {
                return $vue->afficherOptionsReseaux($communeSelectionnee, $reseaux);
            }
        }

        //si on selectionne le reseau automatiquement,
        //on redirige vers la page parametres directment avec les valeurs necessaires
        $url = '/parametres?' . http_build_query([
            'commune' => $communeSelectionnee['insee'],
            'reseau' => $reseauSelectionne['code_reseau'],
        ]);

        header('Location:' . $url, true, 303);
        exit;
        // return '';
    }
}
