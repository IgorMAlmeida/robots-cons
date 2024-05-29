<?php

namespace App\Services;

use App\Services\Curl;
use DOMDocument;


class TokenService extends Curl{

    public function getToken($values): array {
        try {

            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $htmlLoaded = $doc->loadHTML($values['response']);
    
            if (!$htmlLoaded) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                throw new \Exception($errors[0]->message);
            }
    
            libxml_use_internal_errors(false);
            $tags = $doc->getElementsByTagName('input');
            $count = 0;
    
            $token = null;
            foreach ($tags as $tag) {
                // Verificar se o input tem o atributo name com valor 'SECURITYTOKEN'
                if ($tag->getAttribute('name') === 'inputToken' || $tag->getAttribute('name') === 'SECURITYTOKEN') {
                    $token = $tag->getAttribute('value');
                    break; // Parar a busca após encontrar o token
                }
            }
    
            if ($token === null) {
                throw new \Exception('Token não encontrado');
            }
    
            return [
                "status"   => true,
                "response" => $token,
            ];
        } catch (\Exception $e) {
            return [
                "status" => false,
                "response" => "Erro ao processar HTML: " . $e->getMessage(),
            ];
        }
    }
    
}

