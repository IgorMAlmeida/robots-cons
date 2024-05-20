<?php

namespace App\Services;

use App\Services\Curl;

use function PHPSTORM_META\map;

class LoginService extends Curl{

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

   public function portalConsignado($values):array
   {
        try{
            $urlCounter = 1;
            $captcha = (new ResolveImgCaptcha)->resolve([
                "Key"           => $this->anticaptchakey,
                "PathCaptcha"   => $values['imgPath']
            ]);

            if(!$captcha['status'] ) {
                throw new \Exception("Captcha Unsolved!");
            }

            // var_dump($captcha);
            // var_dump($values);exit;
            $loginData = [
                "SECURITYTOKEN" => $values['token'],
                "SECURITYTOKEN" => $values['token'],
                "captchaPanel:captcha" => $captcha['response'],
                "inputToken" => $values['token'],
                "loginButton"=>"1",
                "senha"    => $this->passPortal,
                "trusted"=>"",
                "username" => $this->userPortal,
                "idb_hf_0" => "",
            ];
            // var_dump($loginData);
            $loginData = http_build_query($loginData);
            // var_dump($loginData);

            $params = [
                "url"            => $this->portalConsignadoBase."/home?".$urlCounter."-2.IBehaviorListener.0-tabs-panel-formUserLogin-loginButton",
                "formDataString" => $loginData,
                "cookies"        => $values['cookie'],
                "cookieFile"     => $values['cookieFile'],
                "method"         => "POST",
                "followLocation" => true,
                "headers"        => [
                    'Accept: application/xml, text/xml, */*; q=0.01',
                    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                    'Origin: https://www.portaldoconsignado.com.br',
                    'Pragma: no-cache',
                    'Referer: https://www.portaldoconsignado.com.br/home?'.$urlCounter,
                    'SECURITYTOKEN:' . $values['token'],
                ],
            ];

            $response = $this->get($params);
            // var_dump($response);
            // exit;
            $cookie = (new CookieService())->getCookie();


            return [
                "erro"       =>  false,
                "response"   =>  $response['response'],
                "urlCounter" =>  $urlCounter,
                "cookieFile" =>  $cookie['cookieFile'],
                "cookiePath" =>  $cookie['cookiePath'],
            ];

        }catch (\Exception $e){
                return [
                "erro"     =>  true,
                "response" =>  $e->getMessage()
            ];
        }
    }
}