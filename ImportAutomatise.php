<?php
  
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use EauPotable\models\InsererFichierBD;

if ($argc < 2) {
    echo "Format: php ImportAutomatise.php <(int) parametre>\n";
    exit(1);
}

$date = new DateTime()->modify("-1 month");
$annee = $date->format('Y');
$mois = $date->format('m');
$parametre = $argv[1];

echo "Paramètres : " . $annee . " - " . $mois . " - " . $parametre . "\n";

InsererFichierBD::insererPrelevement($annee,$mois,(int) $parametre);

echo "Insertion terminée.\n";