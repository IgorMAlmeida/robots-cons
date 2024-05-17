<?php

namespace App\Services;

use App\Services\Curl;
use Illuminate\Support\Facades\Http;
use DOMDocument;


class SaveCaptchaImage extends Curl{

    private string $portalConsignado;

    public  function __construct()
    {
        $this->portalConsignado = env('URL_PORTAL_CONSIGNADO');
    }

   public function getImage():array {

      try{

        $data = [
            'url'           => $this->portalConsignado."/home",
            'method'        => 'GET',
            'followLocation'=> true,
            'cookie'        => 'JSESSIONID=0000y1iaLO3IU0Apbbbmy44xT6S:18guqmp5m'
        ];
        
        $response = $this->get($data);
        // var_dump($response);exit;
        // exit;

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
                //verificar pq n ta salvando
                //alterar para tela de login correta
                var_dump($siteImageCaptcha);exit;
                $imageCaptchaDecoded = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $siteImageCaptcha));
                $imagePath = getcwd().'/CaptchaImgs/consignado_'. date('Y_m_d_H_i_s') .'.png';
                file_put_contents($imagePath, $imageCaptchaDecoded);
            }
        }

        // if(strlen($imageCaptchaDecoded) < 100){
        //     unlink($imagePath);
        //     $this->getImage();
        // }

        return [
            "status"    =>  true,
            "imgPath"   => $imagePath
        ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}