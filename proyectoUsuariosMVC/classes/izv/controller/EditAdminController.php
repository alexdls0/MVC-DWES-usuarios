<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;
use izv\database\Database;
use izv\tools\Util;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;

class EditAdminController extends Controller {

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
    
    function doedit(){
        var_export($_POST);
        $nombrechecked = null;
        if(isset($_POST['nombre']) && $_POST['nombre'] !=null){
            if(strlen($_POST['nombre']) <= 50 && ctype_alpha($_POST['nombre'])){
                $nombrechecked = $_POST['nombre'];
            }
        }
        
        if($nombrechecked != null){
            $this->getModel()->cambiarNombre($_SESSION['password'], $_SESSION['email'], $nombrechecked);
        }
        
        $aliaschecked = null;
        if(isset($_POST['alias']) && $_POST['alias'] != null){
            if(strlen($_POST['alias']) <= 30 && !strpos($_POST['alias'], ' ')){
                $aliaschecked = $_POST['alias'];
            }
        }
        
        if($aliaschecked != null){
            $this->getModel()->cambiarAlias($_SESSION['password'], $_SESSION['email'], $aliaschecked);
        }
        
        if(isset ($_POST['vaciaralias'])){
            $this->getModel()->vaciarAlias($_SESSION['password'], $_SESSION['email']);
        }
        
        if(isset($_POST['noadmin'])){
            $this->getModel()->noAdmin($_SESSION['password'], $_SESSION['email']);
        }
        
        $clavevieja = $_SESSION['password'];
        $clavechecked = null;
        if(isset($_POST['clave']) && isset($_POST['claveRep']) && isset($_POST['claveRep2'])){
            if($_POST['clave'] !=null && $_POST['claveRep'] !=null && $_POST['claveRep2'] !=null){
                if(Util::verificarClave($_POST['clave'], $_SESSION['password'])){
                    if($_POST['claveRep'] === $_POST['claveRep2']){
                        if(strlen($_POST['claveRep']) > 8 && strlen($_POST['claveRep']) < 40 && !ctype_digit($_POST['claveRep']) 
                        && !ctype_alpha($_POST['claveRep']) && !strpos($_POST['claveRep'], ' ')){
                            $clavechecked = $_POST['claveRep'];
                            $clavevieja = Util::encriptar($clavechecked);
                        }
                    }    
                }
            }
        }
        
        if($clavechecked != null){
            $this->getModel()->cambiarClave($_SESSION['password'], $_SESSION['email'], $clavevieja);
        }
        
        if(isset($_POST['email']) && $_POST['email'] != null){
            $this->getModel()->cambiarCorreo($clavevieja, $_SESSION['email'], $_POST['email']);   
        }
        
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        header('Location: ' . App::BASE . 'index');
        exit();
    }
   
}