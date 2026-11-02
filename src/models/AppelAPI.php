<?php
final class AppelAPI{

    /**
     * @param string $type 'communes_udi' ou 'resultats_dis'
     * @param array $param liste des paramètres de recherche
     */
    public static function getData(string $type, array $param = ['size' => 20]): array{

        $url = "https://hubeau.eaufrance.fr/api/v1/qualite_eau_potable/" . $type . "?";
        foreach ($param as $p => $value) {
            $url .= $p . "=" . $value ."&";
        }
        $url = substr($url,0,-1); // Enlève le '&' final

        $res = file_get_contents($url);

        return json_decode($res, true);
    }

    public static function test(): void{
        $data = self::getData("resultats_dis", ['size' => 1, 'page' => 2, 'code_commune' => '13001']);
        print_r($data);
    }


}