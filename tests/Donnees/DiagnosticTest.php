<?php

declare(strict_types=1);

namespace EauPotable\Tests\Donnees;

use EauPotable\models\Connexion;
use EauPotable\models\Diagnostic;
use PHPUnit\Framework\TestCase;

/**
 * Ce test a besoin de la base démarrée : `docker compose up -d`.
 */
final class DiagnosticTest extends TestCase
{
    public function testLaBaseRepond(): void
    {
        $diagnostic = new Diagnostic(Connexion::ouvrir());

        self::assertMatchesRegularExpression('/^\d+\.\d+/', $diagnostic->versionPostgresql());
    }
}
