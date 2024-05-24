<?php

namespace App\Services;

use App\Services\Curl;

class CookieService extends Curl{

    private string $portalConsignadoAdm;
       
    public  function __construct()
    {
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');
    }

   public function getCookie($url):array {

      try{
        $cookiePath = getcwd() . '/Cookies';
        $cookieFile = $cookiePath.'/cookie_'.date('Y_m_d_H_i_s');
        $cookie = '';

        $data = [
            'url'           => $this->portalConsignadoAdm.$url,
            'method'        => 'GET',
            'followLocation'=> true,
            'cookie'        => $cookie,
            'cookieFile'    => $cookieFile
        ];
        
        $response = $this->get($data);
        if(!$response['status']){
            throw new \Exception($response['response']);
        }
        
        $cookieContent = file_get_contents($cookieFile);
        if (preg_match('/JSESSIONID\s+([^\s]+)/', $cookieContent, $matches)) {
            $sessionValue = $matches[1];
            $cookie = "JSESSIONID={$sessionValue}";
        } else {
            throw new \Exception("JSESSIONID not found in cookie file");
        }
        
        return [
            "erro"       =>  false,
            "response"   =>  "Cookie saved",
            "cookieFile" =>  $cookieFile,
            "cookiePath" =>  $cookiePath,
            "cookie"     =>  $cookie,
        ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}