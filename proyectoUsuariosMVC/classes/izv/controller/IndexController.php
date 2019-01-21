<?php

namespace izv\controller;

use izv\tools\Session;
use izv\app\App;
use izv\model\Model;
use izv\tools\Reader;

class IndexController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function main() {
        if(isset($_SESSION['email']) && isset($_SESSION['password'])){
            //metodo en el modelo al que le paso clave y correo y devuelve true o false para saber si existe
            $r = $this->getModel()->existeUsuario($_SESSION['password'], $_SESSION['email']);
            if($r==false){
                unset($_SESSION['email']);
                unset($_SESSION['password']);
                unset($_SESSION['name']);
            }else{
                $r = $this->getModel()->esAdmin($_SESSION['password'], $_SESSION['email']);
                if($r){
                    header('Location: ' . App::BASE . 'admin');
                    exit();
                }else{
                    header('Location: ' . App::BASE . 'user');
                    exit();
                }
            }
        }
    }
    
    function login(){
        if(isset($_POST['email']) && isset($_POST['clave'])){
            $r = $this->getModel()->coincide($_POST['clave'], $_POST['email']);
            if($r){
                $this->getModel()->obtenerLogeo($_POST['clave'], $_POST['email']);
            }else{
                unset($_POST['email']);
                unset($_POST['clave']);
            }
        }
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
    function logout(){
        session_start();
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        unset($_SESSION['name']);
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
    function activar(){
        $id = Reader::read('id');
        $code = Reader::read('code');
        $this->getModel()->activarUsuario($id, $code);
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
}