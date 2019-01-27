<?php

namespace izv\controller;

use izv\tools\Session;
use izv\app\App;
use izv\model\Model;
use izv\tools\Reader;

class PaginacionController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function main() {
        if(isset($_SESSION['email']) && isset($_SESSION['password'])){
            //si existe una sesión se encargará de redireccionarla el index
            header('Location: ' . App::BASE . 'index');
            exit();
        }
        
        $ordenes = [
            'id' => 'id',
            'correo' => 'correo',
            'alias' => 'alias',
            'nombre' => 'nombre'
        ];
        $this->getModel()->set('twigFolder', 'twigtemplates/maundy');
        $this->getModel()->set('twigFile', '_paginacion.html');
        $pagina = Reader::read('pagina');
        if($pagina === null || !is_numeric($pagina)) {
            $pagina = 1;
        }
        $orden = Reader::read('orden');
        if(!isset($ordenes[$orden])) {
            $orden = 'id';
        }
        $filtro = Reader::read('filtro');
        $r = $this->getModel()->getPaginacion($pagina, $orden, $filtro);
        $this->getModel()->add($r);
    }
    
}