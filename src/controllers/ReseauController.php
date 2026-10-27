<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\models\ReseauModel;
use EauPotable\views\ReseauView;

final class ReseauController
{
    public function executer(): string
    {
        $modele = new ReseauModel();
        $communes = $modele->getToutesLesCommunes();

        $vue = new ReseauView();
        return $vue->executer($communes);
    }
}
