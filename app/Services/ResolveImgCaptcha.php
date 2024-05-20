<?php

namespace App\Services;

use App\Services\ImageToText;

class ResolveImgCaptcha
{

    public function resolve(array $values):array
    {
        try{
 
            $api = new ImageToText();

            $api->setKey($values['Key']);

            $api->setFile($values['PathCaptcha']);

            $api->setSoftId(0);

            if (!$api->createTask()) {
                echo "API v2 send failed - ".$api->getErrorMessage()."\n";
                exit;
            }


            if (!$api->waitForResult()) {
                throw new \Exception($api->getErrorMessage());
            }

            // unlink($values['PathCaptcha']);
            return [
                "status"    =>  true,
                "response"  =>  $api->getTaskSolution()
            ];
        }catch(\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }
}