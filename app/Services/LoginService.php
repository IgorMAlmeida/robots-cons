<?php

namespace App\Services;

use App\Services\Curl;

class LoginService {

    private string $userPortal;
    private string $passPortal;
    private string $anticaptchakey;
    private string $portalConsignado;
       
    public  function __construct()
    {
        $this->userPortal = env('USER_PORTAL');
        $this->passPortal = env('PASS_PORTAL');
        $this->anticaptchakey = env('ANTICAPTCHA_KEY');
        $this->portalConsignado = env('URL_PORTAL_CONSIGNADO');
    }

   public function portalConsignado($values):array {

      try{
        session_start();
        // imgPath
        $sessionId = session_id();
        $imagePath = getcwd() . '/CaptchaImgs';
        $image = $imagePath."/".$sessionId;

        // var_dump($image);exit;
        $captcha = (new ResolveImgCaptcha)->resolve([
            "WebsiteKey"    => "DXDN-YIW6-DZ61-A7GJ-A0M6-91BN-TAJ0-SRRH-DN70-GLKF-WCRD-KWEP-5LAP-ZUCE-86T5-QBOY",
            "Key"           => $this->anticaptchakey,
            "PathCaptcha"   => $values['imgPath']
        ]);

        var_dump($captcha);


        return [
            "erro"       =>  false,
            "response"   =>  " ",
        ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}