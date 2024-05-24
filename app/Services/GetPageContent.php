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

            var_dump($ajaxArray);

            // var_dump($ajaxObject);
            var_dump($location);
        
            exit;

            return [
                "erro"       =>  false,
                "ajaxResponse"   =>  $ajaxObject,
                "location" =>  $location,
            ];

        }catch (\Exception $e){
                return [
                "erro"     =>  true,
                "response" =>  $e->getMessage()
            ];
        }
    }
}