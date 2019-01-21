<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;

class LoginController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
        $this->getModel()->set('titulo', 'Login Controller');
        $this->getModel()->set('twigFile', '_login.html');
    }
    
    function main() {
        $this->getModel()->set('titulo', 'Log In');
        if(isset($_SESSION['email']) || isset($_SESSION['password'])){
            header('Location: ' . App::BASE . 'index');
            exit();
        }
    }
    
}