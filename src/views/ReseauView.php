<?php

declare(strict_types=1);

namespace EauPotable\views;

final class ReseauView
{
    /**
 * @param array<int, array{nom_commune: string, code_postal: string}> $communes
 */
    public function executer(array $communes): string
    {
        ob_start(); ?>

	    <label for="choix-commune">Rechercher une commune :</label>
        <input list="communes" id="choix-commune" name="commune" >
        <datalist id="communes">
        <?php foreach ($communes as $commune): ?>
            <option value="<?= htmlspecialchars($commune['nom_commune'] . '(' . $commune['code_postal'] . ')') ?>">
        <?php endforeach; ?>
        </datalist>


    <?php
    return (new Layout("Réseau", "Page de recherche des données de réseau", ob_get_clean()))->afficher();
    }

}
