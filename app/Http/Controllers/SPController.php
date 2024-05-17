<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use App\Services\SaveCaptchaImage;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class SPController extends Controller
{
    public function Gov(Request $request) : array
    {
        try{
            $imageCaptcha = (new SaveCaptchaImage())->getImage();

            $login = (new LoginService())->PortalConsignado($imageCaptcha);

            exit;

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
