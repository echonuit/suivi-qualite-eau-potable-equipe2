<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class ReseauModel{
    private PDO $pdo;
    public function __construct() {$this->pdo = Connexion::ouvrir();}
    public function getToutesLesCommunes(): array{
    $requete = $this->pdo->query('SELECT nom_commune, code_postal FROM schema.commune ORDER BY nom_commune, code_postal');
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}
