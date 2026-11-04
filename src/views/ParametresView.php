<?php
declare(strict_types=1);

namespace EauPotable\views;

//parametresView
//Liste des paramètres -->
//Tous les paramètres déjà mesurés sur le réseau,
//nombre de mesures,
//date de la dernière,
// fonction de recherche par nom
final class ParametresView
{
    /**
     * Afficher le reseau selectionné et
     * afficher les paramètres mésurés associés.
     *
     * @param array<string, mixed> $communeSelectionnee
     * @param array<string, mixed> $reseau
     * @param array<int, array<string, string>> $parametres
     */
    public function afficherParametres(array $communeSelectionnee, array $reseau, array $parametres): string
    {

        ob_start(); ?>

        <!-- Affichage partie commune-reseau -->
        <p>
            Commune :
            <strong>
                <?= $this->escape($communeSelectionnee['nom_commune']) ?>
            </strong>
            &gt;
            Réseau :
            <strong><?= $this->escape($reseau['nom_reseau']) ?></strong>
        </p>

        <?php if (!empty($reseau['quartiers'])): ?>
            <p>
                 — Quartier(s) :
                <?= $this->escape($reseau['quartiers']) ?>
            </p>
        <?php endif; ?>
        <br><br>

        <!-- Affichage partie parametres -->
        <h2>Choisir les paramètres mesurés : </h2>

        <form method="GET" action="/reseau" >  <!-- action="/reseau" ou "/parametres selon quel controller va gerer...-->

            <!-- Permet de garder le code reseau entre requetes -->
            <input type="hidden" name="reseau"
                    value="<?= $this->escape($reseau['code_reseau']) ?>" >

            <?php foreach ($parametres as $parametre): ?>
                <div>
                    <input type="checkbox" id="parametre-<?= $this->escape($parametre['code_param_sandre']) ?>"
                            name="parametres[]"
                            value="<?= $this->escape($parametre['code_param_sandre']) ?>" >

                    <label for="parametre-<?= $this->escape($parametre['code_param_sandre']) ?>">
                        <?= $this->escape($parametre['libelle']) ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit">Filtrer résultats</button>
        </form>

        <?php
    return (new Layout("Parametres", "Page de affichage des parametres", (string) ob_get_clean()))->afficher();
    }


    /**
     * Échappe les valeurs pour un affichage HTML sécurisé.
     *
     * @param string|int|null $value
     */
    private function escape(string|int|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
