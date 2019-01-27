<?php

namespace izv\model;

use izv\tools\Util;
use izv\database\Database;
use izv\data\Usuario;
use izv\managedata\ManageUsuario;
use izv\app\App;
use izv\tools\Reader;
use izv\tools\Session;
use izv\tools\Pagination;

class PaginacionModel extends Model {
    
    function getPaginacion($pagina = 1, $orden = 'id', $filtro = null) {
        $total = $this->getTotalPaginacion();
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
            'paginas' => $enlaces,
            'usuarios' => $array,
            'rango' => $paginacion->range(5),
            'orden' => $orden,
            'filtro' => $filtro
        );
    }

    function getTotalPaginacion() {
        $usuarios = 0;
        if($this->getDatabase()->connect()) {
            $sql = 'select count(*) from usuario';
            if($this->getDatabase()->execute($sql)) {
                if($fila = $this->getDatabase()->getSentence()->fetch()) {
                    $usuarios = $fila[0];
                }
            }
        }
        return $usuarios;
    }
    
    //funcion para obtener los datos que debe manejar twig
    /*function getViewData(){
        $data['twigFolder'] = 'twigtemplates/maundy';
        $data['twigFile'] = '_paginacion.html';
        $data['titulo'] = 'Lista de usuarios paginados';
        
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
    }*/
}