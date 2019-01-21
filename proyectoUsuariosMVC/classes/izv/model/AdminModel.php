<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;
use izv\tools\Session;
use izv\tools\Mail;



class AdminModel extends Model {
    
    //función para comprobar que existe una coincidencia de usuario con la base de datos
    function existeAdmin($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();

        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && $clave ==$usuarios[$i]->getClave()){
                return true;
            }        
        }
        
        return false;
    }
    
    function getViewData(){
        $data['twigFolder'] = 'twigtemplates/twig';
        $data['twigFile'] = '_sessionadmin_landing.html';
        $data['titulo'] = 'Bienvenido Admin';
        
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            
            if($_SESSION['email'] == $usuarios[$i]->getCorreo() && $_SESSION['password'] ==$usuarios[$i]->getClave()){

                $activo = 'Si';
                if($usuarios[$i]->getActivo() == 0){
                    $activo = 'No';
                }
                $alias = '-----';
                if(!is_null($usuarios[$i]->getAlias())){
                        $alias = $usuarios[$i]->getAlias();
                }
                $admin = 'Si';
                if($usuarios[$i]->getAdmin() == 0){
                    $activo = 'No';
                }
                $item = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 
                'alias' => $alias, 'activo' => $activo, 'id' => $usuarios[$i]->getId(),
                'admin' => $admin, 'fechalta' => $usuarios[$i]->getFechaalta());
                $data['admin'] = $item;
                $i = count($usuarios);
            }
        }
        
        return $data;
    }
}