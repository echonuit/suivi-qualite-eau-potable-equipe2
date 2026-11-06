<?php
declare(strict_types=1);

namespace EauPotable\views;

//ParametresView feat:Liste des paramètres -->
//Tous les paramètres déjà mesurés sur le réseau,
//nombre de mesures,
//date de la dernière,
// fonction de recherche par nom
final class ParametresView
{
    // /**
    //  * Afficher le reseau selectionné et
    //  * afficher les paramètres mésurés associés.
    //  *
    //  * @param array<string, mixed> $communeSelectionnee
    //  * @param array<string, mixed> $reseau
    //  * @param array<int, array<string, string>> $parametres
    //  */
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

        <form method="GET" action="/parametres" >

            <!-- Permet de garder le code insee de la commune entre requetes -->
            <input type="hidden" name="commune"
                    value="<?= $this->escape($communeSelectionnee['insee']) ?>">
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

    public function afficherRechercheParametres(array $parametres): string
    {// once defined the exact behavior of this method it could be  merged with afficherParametres as one
        // either way, modify to avoid double layout call... decide if one or two different forms are needed

        ob_start(); ?>

        <form method="GET" action="/parametres">
            <label for="choix-parametre">Rechercher un parametre :</label>
            <input
                type="text" id="choix-parametre" list="liste-parametres"
                placeholder="Saisir le nom d'un parametre..." autocomplete="off" required >

            <datalist id="liste-parametres">
                <?php foreach ($parametres as $parametre): ?>
                    <option
                        value="<?= $this->escape($parametre['libelle']) ?>"
                        data-insee="<?= $this->escape($parametre['code_param_sandre']) ?>" >
                    </option>
                    <?php endforeach; ?>
            </datalist>
            <input type="hidden" name="parametre" id="code-sandre-parametre">
            <button type="submit">Chercher</button>
        </form>

        <?php
        return (new Layout('Parametres', 'Page de affichage des parametres', (string) ob_get_clean()))->afficher();
    }

    //     /**
    //      * displays the total number of measurements done for a given network
    //      * this function gets its data from the ParametresModel method
    //      * public function getNombreDesMesuresLieesAuReseau(string $inputCodeReseau): int{
    //      *      $requete = $this->pdo->prepare("SELECT COUNT(rp.code_reseau) ...
    //      */
    //      private function afficherNombreDeMesures(array $nombreMesures): string
    //     {
    //          TODO
    //     }

    //     /**
    //      * displays the date of the last measurement done for a given network
    //      * this function gets its data from the ParametresModel method
    //      * public function getDerniereMesureLieeAuReseau(string $inputCodeReseau): ?string{
    //      *     $requete = $this->pdo->prepare("SELECT p.date_prelevement ...
    //      */
    //     private function afficherDerniereMesure(array $derniereMesure): string
    //     {
    //          TODO
    //     }

    /**
     * Afficher un message si aucune mésure est trouvé pour le reseau selectioné.
     *
     * @param array<string, mixed> $reseau
     */
    public function afficherAucunParametre(array $reseau): string
    {
        ob_start();?>
        <p>
            Aucun prélèvement ou paramètre n'a été trouvé pour le réseau
            <strong>
                <?= $this->escape($reseau['nom_reseau']) ?>
            </strong>
            , pour cette période.
        </p>

        <a href="/reseau">Retour à la selection du reseau</a>
        <br>
        <a href="/parametre">Retour au choix des paramètres</a>

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
