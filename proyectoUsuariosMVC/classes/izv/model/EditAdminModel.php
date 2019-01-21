<?php

namespace izv\model;

use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;
use izv\tools\Util;

class EditAdminModel extends Model {
    
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
        $data['twigFile'] = '_editadmin.html';
        $data['titulo'] = 'Editar perfil de admin';
        
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
                $data['user'] = array('nombre' => $usuarios[$i]->getNombre(), 'correo' => $usuarios[$i]->getCorreo(), 'alias' => $alias, 'activo' => $activo );
                $i = count($usuarios);
            }
        }
        
        return $data;
    }
    
    function cambiarNombre($clave, $correo, $nombrechecked){
        $sql = 'update usuario set nombre = :nombre where correo = :correo AND clave=:clave';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('nombre', $nombrechecked);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('clave', $clave);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function cambiarAlias($clave, $correo, $aliaschecked){
        $sql = 'update usuario set alias = :alias where correo = :correo AND clave=:clave';
            $db = new Database();
            if($db->connect()) {
                $conexion = $db->getConnection();
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindValue('alias', $aliaschecked);
                $sentencia->bindValue('correo', $correo);
                $sentencia->bindValue('clave', $clave);
                $sentencia->execute();
            }
            $db->close();
    }
    
    function cambiarClave($clave, $correo, $clavechecked){
        $sql = 'update usuario set clave = :clavenueva where correo = :correo AND clave = :clave';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('clavenueva', $clavechecked);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('clave', $clave);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function vaciarAlias($clave, $correo){
        $sql = 'update usuario set alias = NULL where correo = :correo AND clave=:clave';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('clave', $clave);
            $sentencia->execute();
        }
        $db->close();
    }
    
    function noAdmin($clave, $correo){
        $sql = 'update usuario set admin = 0 where correo = :correo AND clave=:clave';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('clave', $clave);
            $sentencia->execute();
        }
        $db->close();
    }
    
    //Cambiar el email y envía el correo de activación
    function cambiarCorreo($clave, $correo, $correonuevo){
        $resultado=0;
        $sql = 'update usuario set correo = :correonuevo, activo = 0 where correo = :correo AND clave = :clave';
        $db = new Database();
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correonuevo', $correonuevo);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('clave', $clave);
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
            if($usuarios[$i]->getCorreo() == $correonuevo && $usuarios[$i]->getClave() == $clave){
                $user = $usuarios[$i];
            }
        }
        
        $resultado2=false;
        if($resultado > 0 && $user!=null){
            $resultado2 = Mail::sendActivation($user);
        }
    }
    
}