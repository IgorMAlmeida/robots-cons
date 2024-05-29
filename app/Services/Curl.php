<?php

namespace App\Services;

class Curl
{

    public function performCurlRequest($url, $postFields = null, $headers = [], &$cookie = '', $referer = '') {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $postFields ? 'POST' : 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Não seguir redirecionamentos automaticamente
        curl_setopt($ch, CURLOPT_HEADER, true); // Incluir cabeçalhos na resposta
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
        if ($postFields) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
    
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
    
        $response = curl_exec($ch);
        if ($response === false) {
            echo 'Erro: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }
    
        // Separar cabeçalhos do corpo da resposta
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
    
        // Capturar código de status
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Capturar URL de redirecionamento
        $redirectUrl = '';
        if (preg_match('/^Location:\s*(.*)$/mi', $header, $matches)) {
            $redirectUrl = trim($matches[1]);
        }
    
        // Capturar cookies da resposta
        if (preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $header, $matches)) {
            foreach ($matches[1] as $item) {
                parse_str($item, $cookieArray);
                $cookie .= implode('; ', array_map(
                    function ($v, $k) { return sprintf("%s=%s", $k, $v); },
                    $cookieArray,
                    array_keys($cookieArray)
                )) . '; ';
            }
        }
    
        curl_close($ch);
    
        return [
            'response' => $body,
            'statusCode' => $statusCode,
            'redirectUrl' => $redirectUrl,
            'cookie' => $cookie
        ];
    }
    

    protected function get(array $values):array
    {

        try{
            $ch = curl_init($values['url']);

            if(isset($values['urlCaptcha'])){
                curl_close($ch);
                $ch = curl_init($values['urlCaptcha']);
            }

            if(isset($values['headers']) && !empty($values['headers'])){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $values['headers']);
                curl_setopt($ch, CURLOPT_HEADER, true);
                
            }

            if(isset($values['method']) && !empty($values['method'])){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $values['method']);
            }

            if(isset($values['formDataString'])){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $values['formDataString']);
            }
            
            if(isset($values['cookieFile'])){
                curl_setopt($ch, CURLOPT_COOKIEFILE, $values['cookieFile']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $values['cookieFile']);
            }

            if(isset($values['cookie'])){
                curl_setopt($ch, CURLOPT_COOKIE, $values['cookie']);
            }

            if(isset($values['followLocation'])){
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
            }

            if(isset($values['urlRefer'])){
                curl_setopt($ch, CURLOPT_REFERER, $values['urlRefer']);
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $response = curl_exec($ch);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $info = curl_getinfo($ch);

            if(isset($values['debug']) && $values['debug']){
                // var_dump($info);
            }

            curl_close($ch);

            return [
                "status"      =>  true,
                "response"    =>  $response,
                "effectiveUrl" =>  $effectiveUrl
            ];

        }catch(\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }
}
