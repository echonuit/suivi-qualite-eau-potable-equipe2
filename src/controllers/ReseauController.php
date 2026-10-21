<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\views\ReseauView;

final class ReseauController
{
    public function executer(): string
    {
        $vue = new ReseauView();
        return $vue->executer();
    }
}
