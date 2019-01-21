<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;

class UsersController extends Controller {

   function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function main() {
        if(!isset($_SESSION['email']) || !isset($_SESSION['password'])){
            header('Location: ' . App::BASE . 'index');
            exit();
        }
        $r = $this->getModel()->existeAdmin($_SESSION['password'], $_SESSION['email']);
        if($r==false){
            session_start();
            unset($_SESSION['email']);
            unset($_SESSION['password']);
            unset($_SESSION['name']);
            header('Location: ' . App::BASE . 'index');
            exit();
        }
    }
   
}