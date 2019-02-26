<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;

class SigninController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
        $this->getModel()->set('titulo', 'Signin Controller');
        $this->getModel()->set('twigFile', '_signin.html');
    }
    
    function main() {
        $this->getModel()->set('titulo', 'Sign In');
        if(isset($_SESSION['email']) || isset($_SESSION['password'])){
            header('Location: ' . App::BASE . 'index');
            exit();
        }
    }
    
    function dosign(){
        if(isset($_POST['nombre']) && isset($_POST['alias']) && isset($_POST['email']) && isset($_POST['clave']) && isset($_POST['claveRep'])){
            
            $nombre = $_POST['nombre'];
            $alias = $_POST['alias'];
            $email = $_POST['email'];
            $clave = $_POST['clave'];
            $claveRep = $_POST['claveRep'];
            
            /*Comprobamos que el nombre sea solo alfabeto, sin espacios y no mayor a 50 caracteres*/
            if(strlen($nombre) > 50 || !ctype_alpha($nombre)){
               header('Location: ' . App::BASE . 'index');
               exit();
            }
            
            /*Comprobamos que el alias no tenga mas de 30 caracteres y no tenga espacios*/
            if($alias != null){
                if(strlen($alias) > 30 || strpos($alias, ' ')){
                    header('Location: ' . App::BASE . 'index');
                    exit();
                }
            }
            
            /*Comprobamos que la clave tenga al menos 8 caracteres, no pase los 40, sin espacios y que contenga numeros y letras*/
            if(strlen($clave) < 8 || strlen($clave) > 40|| ctype_digit($clave) || ctype_alpha($clave) || strpos($clave, ' ')){
                header('Location: ' . App::BASE . 'index');
                exit();
            }
            
            if($claveRep != $clave){
                header('Location: ' . App::BASE . 'index');
                exit();
            }
            
            //pasamos toda esta informacion al modelo de sign para que añada al usuario a la base de datos
            //además ese método se encargará de enviar un correo si todo es correcto
            $this->getModel()->registrar($email, $alias, $nombre, $clave);
            
        }
        
        header('Location: ' . App::BASE . 'index');
        exit();
    }
    
}