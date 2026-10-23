<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class ReseauModel{
    public function __construct(private PDO $pdo) {}
    public function getToutesLesCommunes(): array{
    $requete = $this->pdo->query('SELECT nom, code_postal FROM schema.commune ORDER BY nom, code_postal');
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}
