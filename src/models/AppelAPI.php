<?php

declare(strict_types=1);

namespace EauPotable\models;

final class AppelAPI
{
    /**
     * @param string $type 'communes_udi' ou 'resultats_dis'
     * @param array $param liste des paramètres de recherche
     * @return array Les données renvoyée par l'API au format JSON, converties dans un array
     */
    public static function obtenirDonnees(string $type, array $param = ['size' => 20]): array
    {

        $url = "https://hubeau.eaufrance.fr/api/v1/qualite_eau_potable/" . $type . "?";
        foreach ($param as $p => $value) {
            $url .= $p . "=" . $value . "&";
        }
        $url = substr($url, 0, -1); // Enlève le '&' final
        echo $url;

        return self::obtenirDonneesDepuisURL($url);
    }

    /**
     * @param string $url L'url envoyée pour contacter une API
     * @return array Les données renvoyée par l'API au format JSON, converties dans un array
     */
    public static function obtenirDonneesDepuisURL(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Script');
        $response = curl_exec($ch);

        return json_decode($response, true);
    }

    public static function test(): void
    {
        $data = self::obtenirDonnees("resultats_dis", ['size' => 3, 'page' => 2, 'code_commune' => '13001']);
        print_r($data);
    }

}
