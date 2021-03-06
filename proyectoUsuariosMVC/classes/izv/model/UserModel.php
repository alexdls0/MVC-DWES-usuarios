<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;
use izv\tools\Mail;



class UserModel extends Model {
    
    private $correo=null;
    private $clave=null;
    
    //función para comprobar que existe una coincidencia de usuario con la base de datos
    function existeUsuario($clave, $email){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();

        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($email == $usuarios[$i]->getCorreo() && $clave ==$usuarios[$i]->getClave()
            && $usuarios[$i]->getAdmin() == 0 && $usuarios[$i]->getActivo() != 0){
                $this->correo = $email;
                $this->clave = $clave;
                return true;
            }        
        }
        
        return false;
    }
    
     //funcion para obtener los datos que debe manejar twig
    function getViewData(){
        $data['twigFolder'] = 'twigtemplates/twig';
        $data['twigFile'] = '_sessionuser_landing.html';
        $data['titulo'] = 'Bienvenido usuario';
        
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        
        for($i = 0 ; $i < count($usuarios) ; $i++){
            
            if($this->correo == $usuarios[$i]->getCorreo() && $this->clave ==$usuarios[$i]->getClave()){

                $activo = 'Si';
                if($usuarios[$i]->getActivo() == 0){
                    $activo = 'No';
                }
                $alias = '-----';
                if(!is_null($usuarios[$i]->getAlias())){
                        $alias = $usuarios[$i]->getAlias();
                }
                $data['user'] = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 'alias' => $alias, 'activo' => $activo );
                $i = count($usuarios);
            }
        }
        
        return $data;
    }
    
    //funcion para dar de baja a un usuario
    function darDeBaja($clave, $correo){
        //Modifico activo a 0 usando el correo almacenado en la sesion
        $resultado = 0;
        $sql = 'update usuario set activo = 0 where correo = :correo';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $this->correo);
            if($sentencia->execute()) {
                $resultado = $sentencia->rowCount();
            }
            $db->close();
        }

        //Obtengo el usuario que tiene ese correo
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        $user = null;

        for($i = 0 ; $i < count($usuarios); $i++){
            if($usuarios[$i]->getCorreo() == $this->correo){
                $user = $usuarios[$i];
            }
        }

        //Envio un correo de activacion usando ese usuario
        $resultado2=false;
        if($resultado > 0 && $user!=null){
            $resultado2 = Mail::sendActivation($user);
        }
        
        return $resultado2;
    }
    
    function eliminarCuenta($clave, $correo){
        //Elimino el usuario de la base de datos usando la sesion
        $db = new Database();
        $manager = new ManageUsuario($db);
        $resultado = 0;
        
        $resultado = $manager->removeEmail($correo);    
    }
}