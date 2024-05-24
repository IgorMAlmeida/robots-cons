<?php

namespace App\Services;

use App\Services\Curl;
use Illuminate\Support\Facades\Http;
use DOMDocument;


class SaveCaptchaImage extends Curl{

    private string $portalConsignadoBase;
    private string $portalConsignadoAdm;


    public  function __construct()
    {
        $this->portalConsignadoBase = env('URL_PORTAL_CONSIGNADO_BASE');
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');

    }

   public function getImage($values):array {

      try{

        $data = [
            'url'           => $this->portalConsignadoAdm,
            'method'        => 'GET',
            'followLocation'=> true,
            'cookie'        => $values['cookieFile'],
            'cookieFile'    => $values['cookieFile'],
        ];
        $response = $this->get($data);

        $token = (new TokenService())->getToken($response);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($response['response']);
        libxml_use_internal_errors(false);

        $tags = $doc->getElementsByTagName('img');
        $count = 0;

        foreach($tags as $tag)
        {
            $count++;

            if($tag->getAttribute('id') == "cipCaptchaImg")
            {
                $siteImageCaptcha = $tag->getAttribute('src');

                $data = [
                    'url'           => $this->portalConsignadoBase.$siteImageCaptcha,
                    'method'        => 'GET',
                    'followLocation'=> true,
                    'cookie'        => $values['cookieFile'],
                    'cookieFile'    => $values['cookieFile'],
                ];
                $response = $this->get($data);

                if(!$response['status']){
                    throw new \Exception("Erro ao capturar imagem");
                }

                $directoryPath = getcwd().'/CaptchaImgs';
                if (!is_dir($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
                
                $imagePath = $directoryPath.'/consignado_'.date('Y_m_d_H_i_s').'.png';
                if (file_put_contents($imagePath, $response['response']) === false) {
                    throw new \Exception("Erro ao gravar imagem");
                }
            }
        }

        return [
            "status"    => true,
            "imgPath"   => $imagePath,
            "token"     => $token['response'],
        ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}