<?php

namespace App\Http\Controllers;

use App\Services\CookieService;
use App\Services\GovConsultService;
use App\Services\LoginService;
use App\Services\CaptchaImage;
use Illuminate\Http\Request;

class SPController extends Controller
{
    public function Gov(Request $request) : array
    {
        try{
            $cookie = (new CookieService())->getCookie("");
            if($cookie['erro']) {
                throw new \Exception($cookie['response']);
            }

            $imageCaptcha = (new CaptchaImage())->getImage($cookie['response']);
            if($imageCaptcha['erro']) {
                throw new \Exception($imageCaptcha['response']);
            }

            $params = [
                "imgPath"    => $imageCaptcha['response']['imgPath'],
                "token"      => $imageCaptcha['response']['token'],
                "cookie"     => $cookie['response']['cookieFile'],
                "cookieFile" => $cookie['response']['cookieFile'],
                "cookiePath" => $cookie['response']['cookiePath'],
            ];
            
            $login = (new LoginService())->PortalConsignado($params);
            if($login['erro']) {
                throw new \Exception($login['response']);
            }

            $params = [
                "cookie"     => $cookie['response']['cookie'],
                "cookieFile" => $login['response']['cookieFile'],
                "cookiePath" => $login['response']['cookiePath'],
                "pageContent"=> $login['response']['pageContent'],
                "token"      => $imageCaptcha['response']['token'],
            ];

            $govConsult=(new GovConsultService())->Consult([...$params]);
            var_dump($govConsult);exit;

            // unlink($imageCaptcha['imgPath']);
            // unlink($cookie['cookieFile']);
            // exit;
            // unlink($imageCaptcha['imgPath']);
            // unlink($login['cookieFile']);
echo "fim do processo";
            exit;
            var_dump($imageCaptcha);
            var_dump($cookie);
            // exit;
            // unlink($imageCaptcha['imgPath']);
            // unlink($cookie['cookieFile']);
            // exit;

            return [
                'status'    => true,
                'message'   => $login,
            ];
        }catch(\Exception $e){
            return [
                'status'    => false,
                'message'   => $e->getMessage(),
            ];
            
        }

    }
}
