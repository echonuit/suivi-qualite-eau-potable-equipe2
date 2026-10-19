# Déploiement du site

### Le site est déployé est accessible sur Internet à l'adresse suivante : [aqualite.alwaysdata.net](aqualite.alwaysdata.net) .

Docker n'est pas supporté pour les offres gratuites d'Alwaysdata, pour que l'autoloader fonctionne il à fallu l'installer avec ```composer install```.

Mais vu que les instructions de redirections sont contenues dans le fichier ```docker/php/apache.conf```, concue pour docker, il à fallu ajouter la directive supplémentaire ```FallbackResource /index.php``` dans la configuration avancée du site sur Alwaysdata pour que l'autolauder fonctionne sans docker.