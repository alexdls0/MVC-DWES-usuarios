<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;

class IndexModel extends Model {
    
    //función para comprobar que existe una coincidencia de usuario con la base de datos
    function existeUsuario($clave, $email){
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
    
    //funcion para comprobar un correo y una clave no encriptada (útil en los formularios)
    function coincide($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();

        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && Util::verificarClave($clave,$usuarios[$i]->getClave())){
                return true;
            }        
        }
        
        return false;
    }
    
    //funcion para comprobar si el usuario con esa clave y correo es admin o no
    function esAdmin($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && $clave ==$usuarios[$i]->getClave()){
                if($usuarios[$i]->getAdmin() != 0){
                    return true;
                }
            }        
        }
        return false;
    }
    
    //funcion para comprobar si el usuario con esa clave y correo esta activo o no
    function esActivo($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && Util::verificarClave($clave,$usuarios[$i]->getClave())){
                if($usuarios[$i]->getActivo() != 0){
                    return true;
                }
            }        
        }
        return false;
    }
    
    //función para obtener un usuario de la base de datos
    function obtenerClave($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && Util::verificarClave($clave,$usuarios[$i]->getClave())
            && $usuarios[$i]->getActivo() !=0){
                return $usuarios[$i]->getClave();
            }        
        }
        
        return null;
    }
    
    //funcion para activar un usuario
    function activarUsuario($id, $code){
        
        $sendedMail = \Firebase\JWT\JWT::decode($code, App::JWT_KEY, array('HS256'));

        $db = new Database();
        $manager = new ManageUsuario($db);
        $user = $manager->get($id);

        /*Si el id existe y coinciden los correos activo a ese usuario*/
        $resultado = 0;
        if($user !== null && $user->getCorreo() === $sendedMail) {
            $sql = 'update usuario set activo = 1 where id = :id';
            $db = new Database();
            if($db->connect()) {
                $conexion = $db->getConnection();
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindValue('id', $user->getId());
                if($sentencia->execute()) {
                    $resultado = $sentencia->rowCount();
                }
                $db->close();
            }
        }
    }
    
    //funcion para obtener los datos que debe manejar twig
    function getViewData(){
        $data['twigFolder'] = 'twigtemplates/twig';
        $data['twigFile'] = '_nosession_landing.html';
        $data['titulo'] = 'Lista de usuarios registrados';
        
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            $alias = '-----';
            if(!is_null($usuarios[$i]->getAlias())){
                    $alias = $usuarios[$i]->getAlias();
            }
            $item = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 'alias' => $alias );
            $data['lista'][]= $item;
        }
        
        return $data;
    }
}