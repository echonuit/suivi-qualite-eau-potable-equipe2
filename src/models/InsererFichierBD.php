<?php

declare(strict_types=1);

namespace EauPotable\models;

final class InsererFichierBD
{
    public static function insererCSV(string $fichier, string $table, array $colonnes = [], string $delimiteur = ","): void
    {
        $pdo = Connexion::ouvrir();

        if ($flux = fopen("csv/". $fichier,"r")) {

            if ($table === "commune") {
                $query = $pdo->prepare('INSERT INTO schema.commune (insee, nom_commune, code_postal)VALUES (:insee, :nom_commune, :code_postal) ON CONFLICT (insee) DO NOTHING');
            }

            $n = 0;
            while (($ligne = fgetcsv($flux, separator: $delimiteur, escape: "\\")) !== false) {
                if ($n > 0) {
                    if ($table === "commune") {
                        $query->execute(['insee' => $ligne[0],'nom_commune' => $ligne[1],'code_postal' => $ligne[2]]);
                    }
                }

                $n++;
            }

            fclose($flux);

        }
        else{
            echo "Erreur de flux";
        }
    }

    public static function insererCommunesCSV(): void
    {
        InsererFichierBD::insererCSV("communes-france.csv","commune",[0,1,2],";");
    }
}
