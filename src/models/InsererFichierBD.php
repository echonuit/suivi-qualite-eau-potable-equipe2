<?php

declare(strict_types=1);

namespace EauPotable\models;

use RuntimeException;
use Throwable;

final class InsererFichierBD
{
    /**
     * @param int[] $colonnes
     */
    public static function insererCSV(string $fichier, string $table, array $colonnes = [], string $delimiteur = ","): void
    {
        $pdo = Connexion::ouvrir();
        //cette modification limite ce fonction a la table commune
        //empeche $query d'etre no definie d'apres la derniere implementation
        if ($table !== "commune") {
            return;
        }
        if ($flux = fopen("csv/" . $fichier, "r")) {

            //if ($table === "commune") {
            $query = $pdo->prepare('INSERT INTO schema.commune (insee, nom_commune, code_postal)VALUES (:insee, :nom_commune, :code_postal) ON CONFLICT (insee) DO NOTHING');
            // }

            $n = 0;
            while (($ligne = fgetcsv($flux, separator: $delimiteur, escape: "\\")) !== false) {
                if ($n > 0) {
                    //if ($table === "commune") {
                    $query->execute(['insee' => $ligne[0],'nom_commune' => $ligne[1],'code_postal' => $ligne[2]]);
                    //}
                }

                $n++;
            }

            fclose($flux);

        } else {
            echo "Erreur de flux";
        }
    }

    public static function insererCommunesCSV(): void
    {
        InsererFichierBD::insererCSV("communes-france.csv", "commune", [0,1,2], ";");
    }

    public static function insererReseauxQuartiersCSV(string $fichier): void
    {
        $pdo = Connexion::ouvrir();

        $flux = fopen("public/csv/" . $fichier, "r");

        if ($flux === false) {
            throw new RuntimeException("Impossible d'ouvrir le fichier CSV.");
        }

        $insererReseau = $pdo->prepare(
            'INSERT INTO schema.reseau (code_reseau, nom_reseau)
             VALUES (:code_reseau, :nom_reseau)
             ON CONFLICT (code_reseau) DO NOTHING',
        );

        $insererCommuneReseau = $pdo->prepare(
            'INSERT INTO schema.commune_reseau (insee, code_reseau)
             VALUES (:insee, :code_reseau)
             ON CONFLICT (insee, code_reseau) DO NOTHING',
        );

        $insererQuartier = $pdo->prepare(
            'INSERT INTO schema.quartier
                (nom_quartier, insee, code_reseau)
             VALUES (:nom_quartier, :insee, :code_reseau)
             ON CONFLICT (insee, nom_quartier) DO NOTHING',
        );

        //L'execution des requetes et faite dans une transaction qui verifie qu'il n'y a pas d'erreur
        // lors de l'insertion de tous les donnees (les trois tables comprises) et dans les references entre les tables
        // sinon rollback
        try {
            $pdo->beginTransaction();

            fgetcsv($flux, separator: ";", enclosure: '"', escape: "");

            $numeroLigne = 1;
            $nbColonnes = 7; //le fichier csv des udi ou reseaux de l'API Hub'Eau comprend 7 colonnes

            while (($ligne = fgetcsv($flux, separator: ";", enclosure: '"', escape: "", )) !== false) {
                //évite les valeurs null lorsqu'une colonne n'est pas disponible sur une ligne donnée du fichier csv
                foreach ($ligne as $index => $valeur) {
                    $ligne[$index] = trim($valeur ?? '');
                }
                $numeroLigne++;

                if (count($ligne) !== $nbColonnes) {
                    throw new RuntimeException("Nombre de colonnes invalide à la ligne " . $numeroLigne);
                }
                //les colonnes $debutAlim et $annee du fichier ne sont pas traitees
                [$insee,$nomCommune,$nomQuartier,$codeReseau,$nomReseau] = array_map('trim', $ligne);

                $insererReseau->execute(['code_reseau' => $codeReseau,'nom_reseau' => $nomReseau]);

                $insererCommuneReseau->execute(['insee' => $insee,'code_reseau' => $codeReseau]);

                $quartierInconnu = in_array(mb_strtolower($nomQuartier), ['', '-', 'non definie', 'non définie'], true);

                if (!$quartierInconnu) {
                    $insererQuartier->execute(['nom_quartier' => $nomQuartier,'insee' => $insee,'code_reseau' => $codeReseau]);
                }
            }

            $pdo->commit();

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;

        } finally {
            fclose($flux);
        }
    }

    public static function insererReseauxQuartiers(): void
    {
        self::insererReseauxQuartiersCSV("communes-quartiers-reseaux.csv");
    }
}
