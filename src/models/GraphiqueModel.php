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

    public function getMesuresParMois(string $codeReseau, string $debut, string $fin): array
    {
        $sql = "SELECT
                    TO_CHAR(DATE_TRUNC('month', p.date_prelevement), 'YYYY-MM') AS mois,
                    COUNT(*) AS nombre
                FROM schema.reseau_prelevement rp
                JOIN schema.prelevement p ON rp.reference = p.reference
                WHERE rp.code_reseau = :code_reseau
                  AND p.date_prelevement >= :debut
                  AND p.date_prelevement <= :fin
                GROUP BY DATE_TRUNC('month', p.date_prelevement)
                ORDER BY DATE_TRUNC('month', p.date_prelevement)";

        $requete = $this->pdo->prepare($sql);
        $requete->execute([
            'code_reseau' => $codeReseau,
            'debut' => $debut,
            'fin' => $fin,
        ]);

        return array_map(
            static fn(array $ligne): array => [
                'mois' => (string) $ligne['mois'],
                'nombre' => (int) $ligne['nombre'],
            ],
            $requete->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
