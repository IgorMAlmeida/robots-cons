<?php

namespace App\Http\Controllers;

use App\Services\CookieService;
use App\Services\GovConsultService;
use App\Services\LoginService;
use App\Services\ScrappingService;
use App\Services\CaptchaImage;
use App\Http\Requests\GovSPRequest;


class SPController extends Controller
{
    public function Gov(GovSPRequest $request) : array
    {
        try{
            $cpf       = $request->input('cpf');
            $matricula = $request->input('matricula');

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
                "cpf"        => $cpf,
                "matricula"  => $matricula,
            ];

            $govConsult = (new GovConsultService())->Consult([...$params]);

            $scrapping = (new ScrappingService())->getDados($govConsult['response']);

            return [
                'status'    => true,
                'message'   => $scrapping,
            ];
        }catch(\Exception $e){
            return [
                'status'    => false,
                'message'   => $e->getMessage(),
            ];
        }
    }
}
