<?php

declare(strict_types=1);

namespace EauPotable\Pages;

final class Reseau
{
    public function afficher(): string
    {
        ob_start(); ?>

        <p> Test page réseau fonctionnelle </p>

    <?php
    return (new Layout("Réseau","Page de recherche des données de réseau",ob_get_clean()))->afficher();    
    }

}