<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;
use izv\tools\Mail;



class UsersModel extends Model {
    
    private $correo = null; 
    
    function existeAdmin($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();

        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && $clave ==$usuarios[$i]->getClave() && $usuarios[$i]->getAdmin()>0){
                $this->correo = $email;
                return true;
            }        
        }
        
        return false;
    }
    
     //funcion para obtener los datos que debe manejar twig
    function getViewData(){
        $data['twigFolder'] = 'twigtemplates/twig';
        $data['twigFile'] = '_users.html';
        $data['titulo'] = 'Lista de usuarios';
        
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($this->correo != $usuarios[$i]->getCorreo()){
                $alias = '-----';
                if(!is_null($usuarios[$i]->getAlias())){
                        $alias = $usuarios[$i]->getAlias();
                }
                $activo = 'Si';
                if($usuarios[$i]->getActivo() == 0){
                    $activo = 'No';
                }
                $admin = 'Si';
                if($usuarios[$i]->getAdmin() == 0){
                    $admin = 'No';
                }
                
                $item = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 'alias' => $alias,
                              'id' => $usuarios[$i]->getId(),'activo' =>$activo, 'admin' =>$admin);
                $data['lista'][]= $item;
            }
        }
        
        return $data;
    }
}