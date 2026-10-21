<?php

declare(strict_types=1);

namespace EauPotable\controllers;

use EauPotable\views\AccueilView;

final class AccueilController
{
    public function executer(): string
    {
        $vue = new AccueilView();
        return $vue->executer();
    }
}
