<?php
declare(strict_types=1);

namespace EauPotable\views;

use DateTimeImmutable;

// ParametresView -Liste des paramètres comporte: -->
// Tous les paramètres déjà mesurés sur le réseau,
// Nombre de mesures,
// Date de la dernière,
// Une Fonction de recherche par nom
final class ParametresView
{
    // /**
    //  * Affiche la commune, le reseau selectionné, les paramètres mésurés associés
    //  * et les statistiques (nombreMesures et DerniereMesure).
    //  *
    //  * @param array{insee: string, nom_commune: string} $communeSelectionnee
    //  * @param array{code_reseau: string, nom_reseau: string, quartiers: string|null} $reseau
    //  * @param array<int, array{libelle: string,code_param_sandre: string}> $parametres
    //  * @param int $nombreMesures
    //  * @param string|null $derniereMesure
    //  *
    //  * @return string
    //  *
    //  */
    public function afficherParametres(
        array $communeSelectionnee,
        array $reseau,
        array $parametres,
        array $parametresSelectionnes,
        int $nombreMesures,
        ?string $derniereMesure,
    ): string {

        // On evite d'afficher les suggestions de parametres deja selectionnes par l'utilisateur
        $codesSelectionnes = array_fill_keys($parametresSelectionnes, true);

        $parametresNonSelectionnes = [];

        foreach ($parametres as $parametre) {
            $code = $parametre['code_param_sandre'];

            if (!isset($codesSelectionnes[$code])) {
                $parametresNonSelectionnes[] = $parametre;
            }
        }

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
        <div class="parametres-layout">

            <!-- Section de selection de parametres: à placer à GAUCHE (CSS) -->
            <section class="selection-parametres">

                <h2>Choisir les paramètres mesurés : </h2>
                <form method="GET"
                      action="/reseau/<?= rawurlencode($reseau['code_reseau']) ?>"
                      id="formParametres"
                >

                    <!-- Permet de garder le code insee de la commune entre requetes -->
                    <input type="hidden" name="commune"
                            value="<?= $this->escape($communeSelectionnee['insee']) ?>">

                    <!-- Permet de garder les parametres selectionnes -->
                    <input  type="hidden" name="parametres" id="parametresSelectionnes"
                            value="<?= $this->escape(implode(',', $parametresSelectionnes)) ?>"
                    >

                    <!-- Affiche champ de recherche -->
                    <?= $this->afficherRechercheParametres($parametresNonSelectionnes) ?>

                    <hr>

                    <!-- Affichage de tous le parametres disponibles -->
                    <h3>Tous les paramètres mesurés sur ce réseau :</h3>

                        <?php foreach ($parametres as $parametre): ?>

                            <?php $code = $parametre['code_param_sandre']; ?>

                            <div>
                                <input type="checkbox" class="js-parametre"
                                        id="parametre-<?= $this->escape($code) ?>"
                                        data-code="<?= $this->escape($code) ?>"
                                        value="<?= $this->escape($code) ?>"
                                        <?= isset($codesSelectionnes[$code]) ? 'checked' : '' ?>
                                >

                                <label for="parametre-<?= $this->escape($code) ?>">
                                    <?= $this->escape($parametre['libelle']) ?>
                                </label>
                            </div>

                        <?php endforeach; ?>

                        <button type="submit">Afficher les Donnees(Graph)</button>

                        <a href="/reseau/<?= rawurlencode($reseau['code_reseau']) ?>?commune=<?= rawurlencode($communeSelectionnee['insee']) ?>">
                            Réinitialiser
                        </a>

                </form>

                <div>
                    <p>
                        <?= $this->afficherNombreDeMesures($nombreMesures) ?>
                        <?= $this->afficherDerniereMesure($derniereMesure) ?>
                    </p>
                </div>

            </section>

            <hr>

            <!-- Section d'affichage de donnees: à placer à DROITE (CSS) -->

            <section class="resultats-parametres">
                <!-- TODO: ajouter code HTML correspondant ou
                 appel à private function afficherResultats()/afficherGraph() -->

                 <!-- REMARQUE: Pour l'instant, ce code sert uniquement à des fins de TEST et affiche des placeholders... -->
                  <h2>Données des paramètres sélectionnés</h2>

                    <?php if ($parametresSelectionnes === []): ?>

                        <p>Veuillez sélectionner au moins un paramètre.</p>

                    <?php else: ?>

                        <?php foreach ($parametres as $parametre): ?>

                            <?php if (isset($codesSelectionnes[$parametre['code_param_sandre']])): ?>

                                <h3><?= $this->escape($parametre['libelle']) ?></h3>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    <?php endif; ?>
            </section>

        </div>

    <script src="/js/rechercheParametres.js" defer></script>

    <?php
    return (new Layout("Parametres", "Page d'affichage des parametres", (string) ob_get_clean(), ["graphique"]))->afficher();
    }

    // /**
    //  * Affiche le champ de recherche des paramètres.
    //  *
    //  * @param array<int, array{libelle: string, code_param_sandre: string}> $parametres
    //  *
    //  * @return string
    //  */
    private function afficherRechercheParametres(array $parametresNonSelectionnes): string
    {

        ob_start(); ?>

            <label for="rechercheParam">Rechercher un parametre :</label>
            <input
                type="text" id="rechercheParam" list="listeParametres"
                placeholder="Saisir le nom d'un parametre..."
                autocomplete="off">

            <button type="button" id="selectionnerParametre">Selectionner parametre</button>

            <!-- Affichage de suggestions des parametres recherches -->
            <datalist id="listeParametres">

                <?php foreach ($parametresNonSelectionnes as $parametre): ?>
                    <option value="<?= $this->escape($parametre['libelle']) ?>"
                            data-code="<?= $this->escape($parametre['code_param_sandre']) ?>">
                    </option>
                <?php endforeach; ?>

            </datalist>

        <?php
        return  (string) ob_get_clean();
    }

    /**
     * Affiche le nombre total de mesures effectuées pour un réseau donné.
     *
     * @param int $nombreMesures
     * @return string
     *
     */
    private function afficherNombreDeMesures(int $nombreMesures): string
    {
        ob_start(); ?>
            <p> Nombre de mesures effectuées sur ce réseau :
                <strong>
                    <?= $this->escape($nombreMesures) ?>
                </strong>
            </p>

            <?php
        return (string) ob_get_clean();
    }

    /**
     * Affiche la date de la dernière mesure effectuée pour un réseau donné.
     *
     * @param string|null $derniereMesure
     * @return string
     *
     */
    private function afficherDerniereMesure(?string $derniereMesure): string
    {

        ob_start(); ?>
            <p> Dernière mesure sur ce réseau :

             <?php if ($derniereMesure !== null): ?>
                <strong>
                    <?= $this->escape((new DateTimeImmutable($derniereMesure))->format('d/m/Y')) ?>
                </strong>
            <?php else: ?>
                <strong>Aucune mesure disponible</strong>
            <?php endif; ?>
            </p>

            <?php
        return (string) ob_get_clean();
    }

    /**
     * Afficher un message si aucune mésure est trouvé pour le reseau selectioné.
     *
     * @param array<string, mixed> $reseau
     * @return string
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
