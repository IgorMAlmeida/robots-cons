<?php

namespace App\Services;
use DOMDocument;
use DOMXPath;


class ScrappingService
{


    public function getDados($html)
    {
        
        $data = [];
        $patterns = [
            '/CPF - <span id="id35">(.*?)<\/span>/',
            '/Nome - <span id="id36">(.*?)<\/span>/',
            '/&Oacute;rg&atilde;o - <span id="id38">(.*?)<\/span>/',
            '/Identifica&ccedil;&atilde;o - <span id="id39">(.*?)<\/span>/',
            '/M&ecirc;s de Refer&ecirc;ncia da Margem - <span id="id3a">(.*?)<\/span>/',
            '/Data de Processamento da Pr&oacute;xima Folha - <span id="id3b">(.*?)<\/span>/'
        ];

        foreach ($patterns as $pattern) {
            preg_match($pattern, $html, $matches);
            if (!empty($matches)) {
                $data[] = $matches[1];
            } else {
                $data[] = null;
            }
        }

        preg_match_all('/<span style="float: right; text-align: right;">([-\d.,]+)<\/span>/', $html, $matches);

        $values = $matches[1];
        $data[] = [
            "Margem Bruta" => [
                "Consignações facultativas" => $values[0],
                "Cartao Credito" => $values[1],
                "Cartao De beneficio" => $values[2],
            ]
        ];

        $data[] = [
            "Margem Disponível" => [
               "Consignações facultativas" => $values[3],
               "Cartao Credito" => $values[4],
                "Cartao De beneficio" => $values[5],
            ]
        ];

        return $data;
    }
}