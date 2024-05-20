<?php

namespace App\Services;

class SessionService{

    public function __construct(){
        if(!isset( $_SESSION)){
            session_start();
          }
    }

    public function getSessionId() {
        return session_id();
    }

}

