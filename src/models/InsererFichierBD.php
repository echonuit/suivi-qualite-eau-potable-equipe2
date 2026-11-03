<?php

declare(strict_types=1);

namespace EauPotable\models;

use EauPotable\models\AppelAPI;
use RuntimeException;
use Throwable;

final class InsererFichierBD
{
    /**
     * @param string $fichier
     */
    public static function insererCommunesCSV(string $fichier): void
    {
        $pdo = Connexion::ouvrir();

        $flux = fopen("public/csv/" . $fichier, "r");

        if ($flux === false) {
            throw new RuntimeException("Impossible d'ouvrir le fichier CSV.");
        }


        //L'execution des requetes et faite dans une transaction qui verifie qu'il n'y a pas d'erreur
        // lors de l'insertion de tous les donnees (les trois tables comprises) et dans les references entre les tables
        // sinon rollback
        try {
            echo 'Avant transaction';
            $pdo->beginTransaction();

            $query = $pdo->prepare(
                'INSERT INTO schema.commune (insee, nom_commune, departement)
             VALUES (:insee, :nom_commune, :departement)
             ON CONFLICT (insee) DO NOTHING',
            );

            $n = 0;
            while (($ligne = fgetcsv($flux, separator: ";", enclosure: '"', escape: "\\")) !== false) {
                if ($n > 0) {
                    $query->execute(['insee' => $ligne[0], 'nom_commune' => $ligne[1], 'departement' => substr((string) $ligne[0], 0, 2)]);
                }
                $n++;
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

    public static function insererCommunes(): void
    {
        self::insererCommunesCSV("communes-quartiers-reseaux.csv");
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

    
/**
 * @param string $annee L'année des prélèvements à inserer
 * @param int $codeParam Le code paramètre Sise-Eaux à insérer (ex. 1340 = Nitrates)
 * 
 * Insère les données de l'API Hubeau dans la BD (tables: prelevement, reseau_prelevement, resultat) provenant de l'année donnée.
 * L'insertion est fragmentée en années pour limiter la taille des importations et éviter de gérer des millions de tuples d'un coup.
*/
public static function insererPrelevement(string $annee, int $codeParam): void{
        $pdo = Connexion::ouvrir();

        try {
            $queryPrelevement = $pdo->prepare(
                'INSERT INTO schema.prelevement (reference, date_prelevement, conclusion)
                VALUES (:reference, :date_prelevement, :conclusion)
                ON CONFLICT (reference) DO NOTHING',
            );

            $queryResultat = $pdo->prepare(
                'INSERT INTO schema.resultat (reference, code_param_sandre, code_param_siseeaux, code_param_cas, libelle, nature, unite, res_libelle, res_brut)
                VALUES (:reference, :code_param_sandre, :code_param_siseeaux, :code_param_cas, :libelle, :nature, :unite, :res_libelle, :res_brut)
                ON CONFLICT (reference, code_param_siseeaux) DO NOTHING',
            );

            $queryPrelevementReseau = $pdo->prepare(
                'INSERT INTO schema.reseau_prelevement (reference, code_reseau)
                VALUES (:reference, :code_reseau)
                ON CONFLICT (reference, code_reseau) DO NOTHING',
            );

        $resultatAPI = AppelAPI::obtenirDonnees('resultats_dis',['date_max_prelevement' => $annee . "-12-31",'date_min_prelevement' => $annee . "-01-01",'size' => 5000, "code_parametre" => $codeParam]);

            $nbRequetesExecutees = 0;
            $total = $resultatAPI['count'];

            while (true) {

                $nextUrl = $resultatAPI['next'] ?? null;
                $data = $resultatAPI['data'];

                $pdo->beginTransaction();

                try {

                    for ($i = 0; $i < sizeof($data); $i++){

                        $page = $data[$i];

                        $queryPrelevement->execute(['reference' => $page["code_prelevement"], 'date_prelevement' => substr($page["date_prelevement"],0,10), 'conclusion' => $page["conclusion_conformite_prelevement"]]);

                        $queryResultat->execute(['reference'=> $page["code_prelevement"],'code_param_sandre' => $page["code_parametre"], 'code_param_siseeaux' => $page["code_parametre_se"], 'code_param_cas' => $page["code_parametre_cas"], 'libelle' => $page["libelle_parametre"], 'nature' => $page['code_type_parametre'], 'unite' => $page['libelle_unite'], 'res_libelle' => $page['resultat_alphanumerique'], 'res_brut' => $page['resultat_numerique']]);

                        foreach($page['reseaux'] ?? [] as $reseau){
                            $queryPrelevementReseau->execute(['reference'=> $page['code_prelevement'], 'code_reseau' => $reseau['code']]);
                        }

                        $nbRequetesExecutees++;
                        echo 'Insertion : n = ' . $nbRequetesExecutees . ' (' . round(100 * $nbRequetesExecutees / $total, 4) . "%) \n";
                    }

                    $pdo->commit();
                    echo 'COMMIT !';

                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $e;
                }

                

                if($nextUrl !== null) {
                    $resultatAPI = AppelAPI::obtenirDonneesDepuisURL($nextUrl);
                } else {
                    break;
                }
            }

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

}
