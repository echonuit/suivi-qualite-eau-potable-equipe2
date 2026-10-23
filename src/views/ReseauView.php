<?php

declare(strict_types=1);

namespace EauPotable\views;

final class ReseauView
{
    public function executer(array $communes): string
    {
        ob_start(); ?>

	    <label for="choix-commune">Rechercher un commune :</label>
        <input list="communes" id="choix-commune" name="commune" >
        <datalist id="communes">
        <?php foreach ($communes as $commune): ?>
            <option value="<?= htmlspecialchars($commune['nom'].'('. $commune['code_postal'].')') ?>">
        <?php endforeach; ?>
        </datalist>


    <?php
    return (new Layout("Réseau", "Page de recherche des données de réseau", ob_get_clean()))->afficher();
    }

}
