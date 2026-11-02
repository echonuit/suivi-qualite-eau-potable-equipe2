<?php
declare(strict_types=1);

namespace EauPotable\views;

final class ReseauView
{
    /**
     * Affichage du champ de saisie pour la selection de commune.
     *
     * @param array<int, array{
     *     insee: string,
     *     nom_commune: string,
     *     code_postal: string
     * }> $communes
     */
    public function afficherRechercheCommune(array $communes): string
    {
        ob_start(); ?>

        <form method="GET" action="/reseau">
            <label for="choix-commune">Rechercher une commune :</label>
            <input
                type="text" id="choix-commune" list="liste-communes"
                placeholder="Saisir le nom d'une commune..." autocomplete="off" required >

            <datalist id="liste-communes">
                <?php foreach ($communes as $commune): ?>
                    <option
                        value="<?= $this->escape($commune['nom_commune'] . ' (' . $commune['code_postal'] . ')') ?>"
                        data-insee="<?= $this->escape($commune['insee']) ?>" >
                    </option>
                    <?php endforeach; ?>
            </datalist>
            <input type="hidden" name="commune" id="insee-commune">
            <button type="submit">Continuer</button>
        </form>

        <!-- Permet de récuperer le code insee même si l'utilisateur ne le connait pas
         (et ne le voit pas dans la recherche des communes) -->
        <script src="/js/rechercheCommunes.js" defer></script>

        <?php
        return (new Layout("Réseau", "Page de recherche des données de réseau", (string) ob_get_clean()))->afficher();
    }

    /**
     * Afficher un message si aucune reseau et trouvé pour la commune selectionée.
     *
     * @param array<string, mixed> $communeSelectionnee
     */
    public function afficherAucunReseau(array $communeSelectionnee): string
    {

        ob_start();?>
        <p>
            Aucun réseau n'a été trouvé pour la commune
            <strong>
                <?= $this->escape($communeSelectionnee['nom_commune'] . '.') ?>
            </strong>
        </p>

        <a href="/reseau">Retour à la recherche de communes</a>

        <?php
    return (new Layout("Réseau", "Page de recherche des données de réseau", (string) ob_get_clean()))->afficher();
    }

    /**
     * Afficher le seul reseau disponible pour la commune et
     * afficher les paramètres mésurés.
     *
     * @param array<string, mixed> $communeSelectionnee
     * @param array<string, mixed> $reseau
     * @param string $parametres
     */
    public function afficherReseauDirect(array $communeSelectionnee, array $reseau, string $parametres): string
    {

        ob_start(); ?>
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
        <h2>Choisir les paramètres mesurés : </h2>
            <p>
                <?= $this->escape($parametres) ?>
            </p>

        <?php
    return (new Layout("Réseau", "Page de recherche des données de réseau", (string) ob_get_clean()))->afficher();
    }


    /**
     * Afficher les réseaux afin que l'utilisateur puisse sélectionner celui qui correspond.
     *
     * @param array<string, mixed> $communeSelectionnee
     * @param array<int, array{code_reseau: string, nom_reseau: string, quartiers: string|null}> $reseaux
     */
    public function afficherOptionsReseaux(array $communeSelectionnee, array $reseaux): string
    {
        ob_start();?>

        <p>
            Commune sélectionnée :
            <strong>
                <?= $this->escape($communeSelectionnee['nom_commune']) ?>
            </strong>
        </p>

        <form method="GET">
            <!-- Permet de garder le code insee entre requetes -->
            <input type="hidden" name="commune"
                value="<?= $this->escape($communeSelectionnee['insee']) ?>" >

            <label for="choix-reseau">Sélectionner un réseau :</label>
            <select id="choix-reseau" name="reseau" required >

                <option value="" selected disabled> - Choisir un réseau - </option>

                <?php foreach ($reseaux as $reseau): ?>
                    <option value="<?= $this->escape($reseau['code_reseau']) ?>">
                    <?= $this->escape($reseau['nom_reseau']) ?>
                    <?php if (!empty($reseau['quartiers'])): ?>
                        — Quartiers :
                        <?= $this->escape($reseau['quartiers']) ?>
                    <?php endif; ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <button type="submit">Continuer</button>
        </form>

        <?php
        return (new Layout('Réseau', 'Choix du réseau', (string) ob_get_clean()))->afficher();
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
