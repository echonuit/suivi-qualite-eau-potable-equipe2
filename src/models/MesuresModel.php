<?php

declare(strict_types=1);

namespace EauPotable\models;

use PDO;

final class MesuresModel
{
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Connexion::ouvrir();
    }

    //Proposition de model pour la recuperation de donnees, compatible avec ParametresView

    // /**
    //  * @param array<int, string> $codesParametres
    //  * @return array<int, array{
    //  *     code_param_sandre: string,
    //  *     libelle: string,
    //  *     date_mesure: string,
    //  *     valeur: float
    //  * }>
    //  */
    // public function getMesuresPourParametres(
    //     string $codeReseau,
    //     array $codesParametres,
    //     ?string $dateDebut = null,
    //     ?string $dateFin = null
    // ): array
    // {}

}
