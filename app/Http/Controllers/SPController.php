<?php

namespace App\Http\Controllers;

use App\Services\CookieService;
use App\Services\GovConsultService;
use App\Services\LoginService;
use App\Services\SaveCaptchaImage;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class SPController extends Controller
{
    public function Gov(Request $request) : array
    {
        try{
            $cookie = (new CookieService())->getCookie($request);
            $imageCaptcha = (new SaveCaptchaImage())->getImage($cookie);

            $params = [
                "imgPath"    => $imageCaptcha['imgPath'],
                "cookie"     => $cookie['cookieFile'],
                "cookieFile" => $cookie['cookieFile'],
                "cookiePath" => $cookie['cookiePath'],
                "token"      => $imageCaptcha['token'],
            ];
            
            $login = (new LoginService())->PortalConsignado($params);
            $params = [
                "imgPath"    => $imageCaptcha['imgPath'],
                "cookie"     => $login['cookieFile'],
                "cookieFile" => $login['cookieFile'],
                "cookiePath" => $login['cookiePath'],
                "token"      => $imageCaptcha['token'],
            ];
            // var_dump($login);exit;
            $govConsult=(new GovConsultService())->Consult([...$params,...$login]);

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
