<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;
use izv\tools\Reader;
use izv\tools\Util;

class EditUFAController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function main() {
        $_SESSION['id']=Reader::read('id');       
        
        $this->getModel()->setUsuarioEditar(Reader::read('id'));
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
        $nombrechecked = null;
        if(isset($_POST['nombre']) && $_POST['nombre'] !=null){
            if(strlen($_POST['nombre']) <= 50 && ctype_alpha($_POST['nombre'])){
                $nombrechecked = $_POST['nombre'];
            }
        }
        
        if($nombrechecked != null){
            echo ($this->getModel()->cambiarNombre($nombrechecked, $_SESSION['id']));
        }
        
        $aliaschecked = null;
        if(isset($_POST['alias']) && $_POST['alias'] != null){
            if(strlen($_POST['alias']) <= 30 && !strpos($_POST['alias'], ' ')){
                $aliaschecked = $_POST['alias'];
            }
        }
        
        if($aliaschecked != null){
            $this->getModel()->cambiarAlias($aliaschecked, $_SESSION['id']);
        }
        
        if(isset ($_POST['vaciaralias'])){
            $this->getModel()->vaciarAlias($_SESSION['id']);
        }
        
        if(isset($_POST['admin']) && $_POST['admin'] == 'noadmin'){
            $this->getModel()->noAdmin($_SESSION['id']);
        }
        
        if(isset($_POST['admin']) && $_POST['admin'] == 'admin'){
            $this->getModel()->siAdmin($_SESSION['id']);
        }
        
        $clavechecked = $_SESSION['password'];
        if(isset($_POST['claveRep']) && isset($_POST['claveRep2'])){
            if($_POST['claveRep'] !=null && $_POST['claveRep2'] !=null){
                if($_POST['claveRep'] === $_POST['claveRep2']){
                    if(strlen($_POST['claveRep']) > 8 && strlen($_POST['claveRep']) < 40 && !ctype_digit($_POST['claveRep']) 
                    && !ctype_alpha($_POST['claveRep']) && !strpos($_POST['claveRep'], ' ')){
                        $clavechecked = Util::encriptar($_POST['claveRep']);
                    }
                }
            }
        }
        
        if($clavechecked != null){
            $this->getModel()->cambiarClave($clavechecked, $_SESSION['id']);
        }
        
        $correoActualizado = $_SESSION['email'];
        if(isset($_POST['email']) && $_POST['email'] != null){
            $correoActualizado = $_POST['email'];    
            if(isset($_POST['activar'])){
                $this->getModel()->cambiarCorreoActivando($_SESSION['id'], $correoActualizado);       
            }else{
                $this->getModel()->cambiarCorreo($_SESSION['id'], $correoActualizado);       
            }
        }
        
        header('Location: ' . App::BASE . 'index');
        exit();
    }
   
}