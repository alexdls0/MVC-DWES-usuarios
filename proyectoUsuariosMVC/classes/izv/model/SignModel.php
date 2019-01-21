<?php

namespace izv\model;

use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\tools\Mail;
use izv\tools\Util;

class SignModel extends Model {
    
    //funcion para añadir a un usuario a la base de datos y enviar un correo (registrar completamente)
    function registrar($correo, $alias, $nombre, $clave){
        $sql = 'insert into usuario values(null, :correo, :alias, :nombre , :clave, 0, 0, CURRENT_TIMESTAMP)';
        $db = new Database();
        $resultado = 0;
        if($db->connect()) {
            $conexion = $db->getConnection();
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindValue('correo', $correo);
            $sentencia->bindValue('alias', $alias);
            $sentencia->bindValue('nombre', $nombre);
            $sentencia->bindValue('clave', Util::encriptar($clave));
            if($sentencia->execute()) {
                $resultado = $conexion->lastInsertId();
            } 
        }
        $db->close();
        if($resultado > 0) {
            $usuario = new Usuario();
            $usuario->setId($resultado);
            $usuario->setAlias($alias);
            $usuario->setNombre($nombre);
            $usuario->setCorreo($correo);
            $usuario->setClave(Util::encriptar($clave));
            $resultado2 = Mail::sendActivation($usuario);
        }
    }
}