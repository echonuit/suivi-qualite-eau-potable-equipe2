<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\views\ReseauView;
use EauPotable\models\ReseauModel;


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
