<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\views\ReseauView;
use EauPotable\models\ReseauModel;
use EauPotable\models\Connexion;

final class ReseauController
{
    public function executer(): string
    {
        $modele = new ReseauModel(Connexion::ouvrir());
        $communes = $modele->getToutesLesCommunes();

        $vue = new ReseauView();
        return $vue->executer($communes);
    }
}
