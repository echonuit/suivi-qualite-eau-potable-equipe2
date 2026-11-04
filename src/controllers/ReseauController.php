<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\models\ParametresModel;
use EauPotable\models\ReseauModel;
use EauPotable\views\ParametresView;
use EauPotable\views\ReseauView;

final class ReseauController
{
    /**
    * @param array<string, mixed> $requete
    * @return string
    */
    public function executer(array $requete): string
    {
        $reseauModele = new ReseauModel();
        $reseauVue = new ReseauView();
        $paramModele = new ParametresModel();
        $paramVue = new ParametresView();

        $communes = $reseauModele->getToutesLesCommunes();

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
            return $reseauVue->afficherRechercheCommune($communes);
        }

        $reseaux = $reseauModele->getReseauxLieesAuCommune($communeSelectionnee['insee']);

        // si aucun réseau est lié à la commune sélectionnée
        if (count($reseaux) === 0) {
            return $reseauVue->afficherAucunReseau($communeSelectionnee);
        }

        // on récupère le réseau sélectionné par l'utilisateur
        $selectionReseau = trim((string) ($requete['reseau'] ?? ''));

        // valider que le reseau selectioné correspond à la commune souhaitée
        if ($selectionReseau !== '') {
            foreach ($reseaux as $reseau) {

                if ($selectionReseau === $reseau['code_reseau']) {
                    $parametres = $paramModele->getParametresLiesAuReseau($selectionReseau);
                    return $paramVue->afficherParametres($communeSelectionnee, $reseau, $parametres);
                }
            }

            // si reseau pas encore selectionné ou pas valide: on affiche le choix de réseau
            return $reseauVue->afficherOptionsReseaux($communeSelectionnee, $reseaux);
        }
        // si un seul réseau est lié à la commune sélectionnée
        if (count($reseaux) === 1) {
            $reseau = $reseaux[0];

            $parametres = $paramModele->getParametresLiesAuReseau($reseau['code_reseau']);

            return $paramVue->afficherParametres($communeSelectionnee, $reseau, $parametres);
        }
        //si plusieurs reseaux disponibles pour la commune selectionee
        return $reseauVue->afficherOptionsReseaux($communeSelectionnee, $reseaux);
    }
}
