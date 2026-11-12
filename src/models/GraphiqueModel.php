<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class GraphiqueModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connexion::ouvrir();
    }

    public function getDateDernierePrelevement(string $codeReseau): ?string
    {
        $sql = "SELECT MAX(p.date_prelevement)
                FROM schema.reseau_prelevement rp
                JOIN schema.prelevement p ON rp.reference = p.reference
                WHERE rp.code_reseau = :code_reseau";

        $requete = $this->pdo->prepare($sql);
        $requete->execute(['code_reseau' => $codeReseau]);

        $date = $requete->fetchColumn();

        if ($date === false || $date === null) {
            return null;
        }

        return (string) $date;
    }

    public function getMesures(string $codeReseau, string $codeParam, string $debut, string $fin): array
    {
        $sql = "SELECT
                    TO_CHAR(p.date_prelevement, 'YYYY-MM-DD') AS date_prelevement,
                    r.res_brut AS valeur
                FROM schema.reseau_prelevement rp
                JOIN schema.prelevement p ON rp.reference = p.reference
                JOIN schema.resultat r ON r.reference = p.reference
                WHERE rp.code_reseau = :code_reseau
                  AND p.date_prelevement >= :debut
                  AND p.date_prelevement <= :fin
                  AND r.code_param_sandre = :code_param
                ORDER BY p.date_prelevement";

        $requete = $this->pdo->prepare($sql);
        $requete->execute([
            'code_reseau' => $codeReseau,
            'debut' => $debut,
            'fin' => $fin,
            'code_param' => $codeParam,
        ]);

        return array_map(
            static fn(array $ligne): array => [
                'date' => (string) $ligne['date_prelevement'],
                'valeur' => (float) $ligne['valeur'],
            ],
            $requete->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /**
     * @param string $codeParam code paramètre sandre
     * @return int valeur du seuil à ne pas dépasser dans les prélèvements
     */
    public function getLimite(string $codeParam){
        $sql = "SELECT valeur
                FROM schema.limite
                WHERE code_param_sandre = :code_param";

        $requete = $this->pdo->prepare($sql);
        $requete->execute(['code_param' => $codeParam]);

        $limite = $requete->fetchColumn();

        return (int) $limite;
    }
}
