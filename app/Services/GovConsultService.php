<?php

namespace App\Services;

use App\Services\Curl;

class GovConsultService extends Curl{

    private string $userPortal;
    private string $passPortal;
    private string $anticaptchakey;
    private string $portalConsignadoBase;
    private string $portalConsignadoAdm;
       
    public  function __construct()
    {
        $this->userPortal = env('USER_PORTAL');
        $this->passPortal = env('PASS_PORTAL');
        $this->anticaptchakey = env('ANTICAPTCHA_KEY');
        $this->portalConsignadoBase = env('URL_PORTAL_CONSIGNADO_BASE');
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');

    }

   public function Consult($values):array
   {
        try{
            $urlCounter = $values['urlCounter'];
            $urlCounter+=2;

            $formData = [
                // "id89_hf_0" => "",
                "radioGroup" => "radio2",
                "SECURITYTOKEN" => $values['token'],
                "acessar" => "1",
                // "id24_hf_0" => "",
                // "cpfServidor" => "100.619.708-75",
                // "matriculaServidor" => "5866005",
                // "selectOrgao" => "",
                // "selectProduto" => "",
                // "selectEspecie" => "",
                // "SECURITYTOKEN" => $values['token'],
                // "botaoPesquisar" => "1",
            ];

            // var_dump($loginData);
            $formData = http_build_query($formData);
            var_dump($values['cookieFile']);
// exit;
            $params = [
                "url"            => $this->portalConsignadoBase."/consignatario/autenticado?".$urlCounter,
                // "formDataString" => $formData,
                "cookies"        => $values['cookie'],
                "cookieFile"     => $values['cookieFile'],
                "method"         => "GET",
                "followLocation" => true,
                // "debug"          => true,
                "headers"        => [
                    'Accept: application/xml, text/xml, */*; q=0.01',
                    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                    'SECURITYTOKEN:'.$values['token'],
                    // 'Referer: https://www.portaldoconsignado.com.br/selecaoPerfil?'.$urlCounter,
                    'Referer: https://www.portaldoconsignado.com.br/wicket/page?'.$urlCounter,
                    'Origin: https://www.portaldoconsignado.com.br',
                    'Pragma: no-cache',
                ],
            ];
            var_dump($params);

            $response = $this->get($params);
            var_dump($response);
            exit;

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