<?php

use EauPotable\models\InsererFichierBD;

require_once __DIR__ . '/vendor/autoload.php';

// ajout les fonctions a appeler ici:
// InsererFichierBD::insererCommunes();
// InsererFichierBD::insererReseauxQuartiers();
InsererFichierBD::insererPrelevement('2026','11',1340);

//dans la terminal utilise par exemple:
// docker compose exec app php importerBD.php
echo "Import terminé !\n";
