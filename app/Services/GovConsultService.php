<?php

namespace App\Services;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Services\Curl;
use App\Helpers\HeaderRequest;


class GovConsultService extends Curl{

    private string $userPortal;
    private string $passPortal;
    private string $anticaptchakey;
    private string $portalConsignadoBase;
    private string $portalConsignadoAuth;
    private string $portalConsignadoAdm;
       
    public  function __construct()
    {
        $this->userPortal = env('USER_PORTAL');
        $this->passPortal = env('PASS_PORTAL');
        $this->anticaptchakey = env('ANTICAPTCHA_KEY');
        $this->portalConsignadoBase = env('URL_PORTAL_CONSIGNADO_BASE');
        $this->portalConsignadoAuth = env('URL_PORTAL_CONSIGNADO_AUTENTICADO');
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');

    }

   public function Consult($values):array
   {
        try{
            $ajaxBaseWicket =  basename($values["pageContent"]["location"]);

            $headers = HeaderRequest::getHeader([
                "token"          => $values['token'],
                "referer"        => $values["pageContent"]["location"],
                "ajaxBaseWicket" => $ajaxBaseWicket
            ]);

            $queryParams = str_replace('./', '/', $values["pageContent"]["ajaxResponse"]["u"]);
            $requestMethod = $values["pageContent"]["ajaxResponse"]["m"];
            $formButtonParam = $values["pageContent"]["ajaxResponse"]["f"]."_hd_0";

            $formData = [
                $formButtonParam => "",
                "radioGroup"     => "radio2",
                "SECURITYTOKEN"  => $values['token'],
                "acessar"        => "true",
            ];
            $formData = http_build_query($formData);
            $url = $this->portalConsignadoBase.$queryParams;

            $params = [
                "url"            => $url,
                "formDataString" => $formData,
                "cookies"        => $values['cookie'],
                "cookieFile"     => $values['cookieFile'],
                "method"         => $requestMethod,
                "followLocation" => true,
                "headers"        => $headers['response'],
            ];

            $response = $this->get($params);
            $getPageContent = (new HeaderContent())->getContent($response['response']);
            $getPageContent = $getPageContent['response'];

            $cookie = explode(';', $getPageContent["cookies"]);
            $cookie = $cookie[0];

            $referer = $getPageContent["ajaxLocation"];
            $headers = HeaderRequest::getHeader([
                "referer"        => $referer,
                "accept"         => "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
            ]);

            
            $params = [
                "url"            => $this->portalConsignadoAuth,
                "cookies"        => $values['cookie'],
                "cookieFile"     => $values['cookieFile'],
                "method"         => "GET",
                "followLocation" => true,
                "headers"        => $headers['response'],
            ];

            $response = $this->get($params);
            $referUrl = "Referer: ".$response['effectiveUrl'];

        
            /////// REQUISITAR TOKENS ////////////


            $token = $values['token'];
            $headerToken = "SECURITYTOKEN:". $token;


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/csrfTokenS');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: */*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                'Referer: https://www.portaldoconsignado.com.br/consignatario/autenticado?5',
                'Sec-Fetch-Dest: script',
                'Sec-Fetch-Mode: no-cors',
                'Sec-Fetch-Site: same-origin',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            
            $response = curl_exec($ch);
            
            curl_close($ch);
    
            $response = curl_exec($ch);

            if (preg_match('/\("SECURITYTOKEN",\s*"([^"]+)"\);/', $response, $matches)) {
                $tokenBusca = $matches[1];
                // echo "Token: " . $token;
            }            
            
            // curl_close($ch);
            // var_dump($response);
            // exit;

            $params = [
                "url"            => 'https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem',
                "cookie"         => $cookie,
                "cookieFile"     => $values['cookieFile'],
                "method"         => "GET",
                "followLocation" => true,
                "headers"        => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                    'Cache-Control: no-cache',
                    'Connection: keep-alive',
                    'Pragma: no-cache',
                    // 'Referer: https://www.portaldoconsignado.com.br/consignatario/autenticado?5',
                    $referUrl,
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: same-origin',
                    'Sec-Fetch-User: ?1',
                    'Upgrade-Insecure-Requests: 1',
                    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                    'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "Linux"',
                ]
            ];

            $response = $this->get($params);
            $referUrl = "Referer:".$response['effectiveUrl'];

            /////// FIM REQUISITAR TOKENS ////////////

            $getPageContent = (new HeaderContent())->getContent($response['response']);
            $getPageContent = $getPageContent['response'];

            $followUrl = str_replace('./', '/', $getPageContent["ajaxResponse"]["u"]);
            $method = $getPageContent["ajaxResponse"]["m"];
            $formButton = $getPageContent["ajaxResponse"]["f"]."_hd_0";

            $referUrl = "Referer: $referUrl";
            
        
            $headerToken = "SECURITYTOKEN: $tokenBusca";
            $formData = [
                $formButton => "",
                "cpfServidor" => "100.619.708-75",
                "matriculaServidor" => "5866005",
                "selectOrgao" => "",
                "selectProduto" => "",
                "selectEspecie" => "",
                "SECURITYTOKEN" => $tokenBusca,
                "botaoPesquisar" => "1",
            ];
            $formData = http_build_query($formData);

            $params = [
                "url"            => 'https://www.portaldoconsignado.com.br/consignatario/'.$followUrl,
                "formDataString" => $formData,
                "cookie"         => $cookie,
                "cookieFile"     => $values['cookieFile'],
                "method"         => $method,
                "followLocation" => true,
                "headers"        => [
                    'Accept: application/xml, text/xml, */*; q=0.01',
                    'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                    'Cache-Control: no-cache',
                    'Connection: keep-alive',
                    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                    'Origin: https://www.portaldoconsignado.com.br',
                    'Pragma: no-cache',
                    $referUrl,
                    $headerToken,
                    'Sec-Fetch-Dest: empty',
                    'Sec-Fetch-Mode: cors',
                    'Sec-Fetch-Site: same-origin',
                    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                    'Wicket-Ajax: true',
                    'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?7',
                    'Wicket-FocusedElementId: id23',
                    'X-Requested-With: XMLHttpRequest, CSRF Prevention',
                    'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "Linux"',
                ],
            ];

            $response = $this->get($params);
            var_dump($response);exit;

            return [
                "erro"       =>  false,
                "response"   =>  $response['response'],
            ];

        }catch (\Exception $e){
                return [
                "erro"     =>  true,
                "response" =>  $e->getMessage()
            ];
        }
    }
}