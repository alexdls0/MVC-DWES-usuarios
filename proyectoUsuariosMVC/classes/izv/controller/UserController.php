<?php

namespace izv\controller;

use izv\tools\Session;
use izv\app\App;
use izv\model\Model;
use izv\tools\Reader;

class UserController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function main() {
        if(!isset($_SESSION['email']) || !isset($_SESSION['password'])){
            header('Location: ' . App::BASE . 'index');
            exit();
        }
        $r = $this->getModel()->existeUsuario($_SESSION['password'], $_SESSION['email']);
        if($r==false){
            session_start();
            unset($_SESSION['email']);
            unset($_SESSION['password']);
            unset($_SESSION['name']);
            header('Location: ' . App::BASE . 'index');
            exit();
        }
    }
    
    function darBaja() {
        $r = $this->getModel()->darDeBaja($_SESSION['password'], $_SESSION['email']);
        session_start();
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        unset($_SESSION['name']);
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
    function eliminar() {
        $r = $this->getModel()->eliminarCuenta($_SESSION['password'], $_SESSION['email']);
        session_start();
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        unset($_SESSION['name']);
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
}