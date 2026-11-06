<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class ReseauModel
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Connexion::ouvrir();
    }
    /**
    * @return array<int, array{
     *     insee: string,
     *     nom_commune: string,
     *     departement: string
     * }>
     */
    public function getToutesLesCommunes(): array
    {
        $requete = $this->pdo->prepare('SELECT insee, nom_commune, departement FROM schema.commune ORDER BY nom_commune, departement');
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array{
     *     code_reseau: string,
     *     nom_reseau: string,
     *     quartiers: string|null
     * }>
     */
    public function getReseauxLieesAuCommune(string $inputCommuneInseeCode): array
    {
        $requete = $this->pdo->prepare(
            "SELECT r.code_reseau, r.nom_reseau,
            STRING_AGG(DISTINCT q.nom_quartier, ', ' ORDER BY q.nom_quartier) AS quartiers
            --regroupement des quartiers pour n'avoir qu'un seul record reseau avec plusieurs ou null quartiers disponibles
         FROM schema.commune_reseau cr
         JOIN schema.reseau r
            ON r.code_reseau = cr.code_reseau
         LEFT JOIN schema.quartier q
            ON q.code_reseau = r.code_reseau
            AND q.insee = cr.insee
         WHERE cr.insee = :insee
         GROUP BY r.code_reseau, r.nom_reseau
         ORDER BY r.nom_reseau",
        );

        $requete->execute(['insee' => $inputCommuneInseeCode]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array{insee: string, nom_commune: string}|null
     * }>
     */
    public function getCommuneParCodeInsee(string $inseeCode): ?array
    {
        $requete = $this->pdo->prepare(
            "SELECT insee, nom_commune
            FROM schema.commune
            WHERE insee = :inseeCode",
        );

        $requete->execute(['inseeCode' => $inseeCode]);
        $commune = $requete->fetch(PDO::FETCH_ASSOC);

        return $commune !== false ? $commune : null;
    }
}
