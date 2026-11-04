<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class ParametresModel
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Connexion::ouvrir();
    }


    /**
     * Récupère les paramètres mesurés (tous les paramètres disponibles)
     * pour le réseau selectionné par l'utilisateur (d'après son code_reseau)
     *
     * @return array<int, array{
     *     libelle: string,
     *     code_param_sandre: string
     * }>
     */
    public function getParametresLiesAuReseau(string $inputCodeReseau): array
    {
        $requete = $this->pdo->prepare(
            "SELECT DISTINCT r.libelle, r.code_param_sandre
            FROM schema.reseau_prelevement rp
            JOIN schema.prelevement p ON rp.reference = p.reference
            JOIN schema.resultat r ON r.reference = p.reference
            WHERE rp.code_reseau = :code_reseau
            -- Jusqu'ici, nous obtenons tous les paramètres mesurés pour ce réseau
            --> si on veut ajouter le choix-de-periode, alors on peut ajouter:
	            -- AND (p.date_prelevement BETWEEN :datePeriodeInf AND :datePeriodeSup )
	            -- GROUP BY p.reference, r.libelle, r.code_param_sandre
    ORDER BY r.libelle ASC",
        );
        // if (datePeriodeSup == null){
        //     datePeriodeSup = date(YYYY-MM-dd, current_date());

        $requete->execute(['code_reseau' => $inputCodeReseau]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le nombre total de mesures associées au réseau sélectionné.
     *
     * @return int
     */
    public function getNombreDesMesuresLieesAuReseau(string $inputCodeReseau): int
    {
        $requete = $this->pdo->prepare(
            "SELECT COUNT(rp.code_reseau)
            FROM schema.reseau_prelevement rp
            JOIN schema.prelevement p ON rp.reference = p.reference
            WHERE rp.code_reseau = :code_reseau
            ",
            // SELECT count(reference)
            // FROM schema.prelevement p
            // WHERE reference in
            //     (SELECT reference
            //     FROM schema.reseau_prelevement rp
            //     WHERE rp.code_reseau = :code_reseau)
        );

        $requete->execute(['code_reseau' => $inputCodeReseau]);
        return ((int) $requete->fetchColumn());

    }

    /**
     * Récupère la date du dernier prélèvement associé au réseau sélectionné par l'utilisateur.
     *
     * @return string|null
     */
    public function getDerniereMesureLieeAuReseau(string $inputCodeReseau): ?string
    {
        $requete = $this->pdo->prepare(
            "SELECT p.date_prelevement --p.reference, peut etre necessaire apres pour valider
            FROM schema.reseau_prelevement rp
            JOIN schema.prelevement p ON rp.reference = p.reference
            WHERE rp.code_reseau = :code_reseau
            AND p.date_prelevement =
                (SELECT MAX(date_prelevement)
                FROM schema.reseau_prelevement rp
                JOIN schema.prelevement p ON rp.reference = p.reference
                WHERE rp.code_reseau = :code_reseau)
            LIMIT 1; --on assure un seul resultat
    ",
        );

        $requete->execute(['code_reseau' => $inputCodeReseau]);
        $resultat = $requete->fetchColumn();
        return $resultat !== false ? (string) $resultat : null;
    }
}
