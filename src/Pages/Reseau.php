<?php

declare(strict_types=1);

namespace EauPotable\Pages;

final class Reseau
{
    public function afficher(): string
    {
        ob_start(); ?>

        <form>
            <label for="ville"> Entrez le nom de votre ville : </label>
            <input type="text" id="ville">
        </form>

    <?php
    return (new Layout("Réseau","Page de recherche des données de réseau",ob_get_clean()))->afficher();    
    }

}