<?php

namespace App\Services;

class GetPageContent {


   public function getContent($html):array
   {
        try{

            preg_match('/^Location:\s*(.+)$/mi', $html, $locationMatches);
            $location = $locationMatches[1] ?? null;

            // Expressão regular para o objeto AJAX
            preg_match('/Wicket\.Ajax\.ajax\((\{.+?\})\);/s', $html, $ajaxMatches);
            $ajaxObject = $ajaxMatches[1] ?? null;
            $ajaxArray = json_decode($ajaxObject, true);

            preg_match('/^Ajax-Location:\s*(.+)$/mi', $html, $locationMatches);
            $ajaxLocation = $locationMatches[1] ?? null;

            preg_match('/^Set-Cookie:\s*(.+)$/mi', $html, $locationMatches);
            $cookies = $locationMatches[1] ?? null;

            // var_dump($cookies);
            // var_dump($ajaxLocation);
        
            // exit;

            return [
                "erro"          =>  false,
                "ajaxResponse"  =>  $ajaxArray,
                "location"      =>  $location,
                "cookies"       =>  $cookies,
                "ajaxLocation"  =>  $ajaxLocation,

            ];

        }catch (\Exception $e){
                return [
                "erro"     =>  true,
                "response" =>  $e->getMessage()
            ];
        }
    }
}