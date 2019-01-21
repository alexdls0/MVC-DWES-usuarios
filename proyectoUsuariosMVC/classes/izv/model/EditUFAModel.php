<?php

namespace izv\model;

use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;
use izv\tools\Util;

class EditUFAModel extends Model {
    
    private $nombreUsuarioAEditar='';
    private $id;
    
    function setUsuarioEditar($id){
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();

        for($i = 0 ; $i < count($usuarios) ; $i++){
            if($id ==$usuarios[$i]->getId()){
                $this->nombreUsuarioAEditar = $usuarios[$i]->getNombre();
                $this->id = $usuarios[$i]->getId();
            }        
        }
    }
    
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
    
    //funcion para obtener los datos que debe manejar twig
    function getViewData(){
        $data['twigFolder'] = 'twigtemplates/twig';
        $data['twigFile'] = '_edituserfromadmin.html';
        $data['titulo'] = 'Editar a usuario '.$this->nombreUsuarioAEditar.' desde perfil administrador';
        return $data;
    }
    
    function cambiarNombre($nombre, $id){
        $sql = 'update usuario set nombre = :nombre where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('nombre', $nombre);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
            return $this->id;
        }
        $db->close();
    }
    
    function cambiarAlias($alias, $id){
        $sql = 'update usuario set alias = :alias where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('alias', $alias);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function cambiarClave($clave, $id){
        $sql = 'update usuario set clave = :clavenueva where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('clavenueva', $clave);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function vaciarAlias($id){
        $sql = 'update usuario set alias = NULL where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function noAdmin($id){
        $sql = 'update usuario set admin = 0 where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function siAdmin($id){
        $sql = 'update usuario set admin = 1 where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('id', $id);
            $sentencia->execute();
        }
        $db->close();
    }
    
    //Cambiar el email y envía el correo de activación
    function cambiarCorreo($id, $correo){
        $resultado=0;
        $sql = 'update usuario set correo = :correo, activo = 0 where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('id', $id);
            if($sentencia->execute()){
                $resultado = 1;    
            }
        }
            
        $db->close();
        
        $db = new Database();
        $manager = new ManageUsuario($db);
        $usuarios = $manager->getAll();
        $db->close();
        $user = null;
         
        for($i = 0 ; $i < count($usuarios); $i++){
            if($usuarios[$i]->getCorreo() == $correo && $usuarios[$i]->getId() == $id){
                $user = $usuarios[$i];
            }
        }
        
        $resultado2=false;
        if($resultado > 0 && $user!=null){
            $resultado2 = Mail::sendActivation($user);
        }
    }
    
    function cambiarCorreoActivando($id, $correo){
        $resultado=0;
        $sql = 'update usuario set correo = :correo, activo = 1 where id = :id';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('id', $id);
            if($sentencia->execute()){
                $resultado = 1;    
            }
        }
            
        $db->close();
    }
    
}