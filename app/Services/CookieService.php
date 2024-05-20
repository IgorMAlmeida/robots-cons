<?php

namespace App\Services;

use App\Services\Curl;

class CookieService extends Curl{

    private string $portalConsignadoAdm;
       
    public  function __construct()
    {
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');
    }

   public function getCookie():array {

      try{
        $sessionId = (new SessionService)->getSessionId();
        $cookiePath = getcwd() . '/Cookies/';
        $cookieFile = $cookiePath.$sessionId;
        $cookie = '';

        $data = [
            'url'           => $this->portalConsignadoAdm,
            'method'        => 'GET',
            'followLocation'=> true,
            'cookie'        => $cookie,
            'cookieFile'    => $cookieFile
        ];
        
        $response = $this->get($data);

        if(!$response['status']){
            throw new \Exception($response['response']);
        }

        return [
            "erro"       =>  false,
            "response"   =>  "Cookie saved",
            "cookieFile" =>  $cookieFile,
            "cookiePath" =>  $cookiePath,
        ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}