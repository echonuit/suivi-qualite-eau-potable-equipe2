<?php

declare(strict_types=1);

namespace EauPotable\Pages;

/**
 * Exemple fourni avec le squelette : la page d'accueil dit si l'environnement est prêt.
 *
 * À remplacer par votre première vraie page. Elle montre la forme d'une page :
 * elle appelle les autres couches, puis rend du HTML dont chaque valeur est échappée.
 */
final class Accueil
{
    public function afficher(): string
    {
        ob_start(); ?>

<div>
    <h2>Aqualité, le service gratuit de suivi de la qualité de l'eau potable</h2>
    <p>
       Pouvoir connaître la qualité de son eau est essentiel pour une bonne consomation en France. <br>
       Certains services officiels fournissent déjà une solution à ce problème, mais offrent une accessibilité et une ergonomie limitée pour différents types d'utilisateurs.<br>
       Avec Aqualité, vous pouvez rechercher votre commune et connaître en un simple clic si l'eau de votre robinet est vraiment propre à la consommation.<br>
       Aqualité offre la possibilité de filtrer les recherches par catégories de polluants (pesticides, nitrates...) pour une recherche plus pertinante ainsi que la possibilité d'exporter vos résultats de recherche pour les utiliser ailleurs ou les partager à autrui.

       Commencez à rechercher dès maintenant :
    </p>
    <a href="/reseau"> Rechercher une commune </a>
</div>

<?php
        return (new Layout("Aqualité", "Page d'accueil", ob_get_clean()))->afficher();
    }

    private static function echapper(string $texte): string
    {
        return htmlspecialchars($texte, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
