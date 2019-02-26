<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;
use izv\tools\Pagination;

class PaginacionModel extends Model {
    
    function getPaginacion($pagina = 1, $orden = 'id', $filtro = null) {
        $total = $this->getTotalPaginacion($filtro);
        $paginacion = new Pagination($total, $pagina);
        $offset = $paginacion->offset();
        $rpp = $paginacion->rpp();
        $parametros = array(
            'offset' => array($offset, \PDO::PARAM_INT),
            'rpp' => array($rpp, \PDO::PARAM_INT)
        );
        if($filtro === null) {
            $sql = 'select * from usuario order by '. $orden .', correo, alias, nombre, id limit :offset, :rpp';
        } else {
            $sql = 'select * from usuario
                    where id like :filtro or correo like :filtro or alias like :filtro or nombre like :filtro
                    order by '. $orden .', correo, alias, nombre, id limit :offset, :rpp';
            $parametros['filtro'] = '%' . $filtro . '%';
        }
        $array = [];
        if($this->getDatabase()->connect()) {
            if($this->getDatabase()->execute($sql, $parametros)) {
                while($fila = $this->getDatabase()->getSentence()->fetch()) {
                    $objeto = new Usuario();
                    $objeto->set($fila);
                    $array[] = $objeto;
                }
            }
        }
        
        $enlaces = $paginacion->values();
        return array(
            'pagina' => $pagina,
            'paginas' => $enlaces,
            'usuarios' => $array,
            'rango' => $paginacion->range(5),
            'orden' => $orden,
            'filtro' => $filtro
        );
    }

    function getTotalPaginacion($filtro = null) {
        $usuarios = 0;
        $parametros = array();
        if($this->getDatabase()->connect()) {
            if ($filtro == null) {
                $sql = 'select count(*) from usuario';
            } else {
                $sql = 'select count(*) from usuario
                        where id like :filtro or nombre like :filtro or correo like :filtro 
                        or alias like :filtro or fechaalta like :filtro';
                $parametros['filtro'] = '%' . $filtro . '%';
            }
            if($this->getDatabase()->execute($sql, $parametros)) {
                if($fila = $this->getDatabase()->getSentence()->fetch()) {
                    $usuarios = $fila[0];
                }
            }
        }
        $this->__destruct();
        return $usuarios;
    }
   
}