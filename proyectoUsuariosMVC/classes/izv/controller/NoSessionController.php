<?php

namespace izv\controller;

use izv\app\App;
use izv\model\Model;
use izv\tools\Session;

class NoSessionController extends Controller {

    function __construct(Model $model) {
        parent::__construct($model);
        $this->getModel()->set('titulo', 'NoSession Controller');
        $this->getModel()->set('twigFile', '_base.html');
    }
    
    function main() {
        $this->getModel()->set('titulo', 'Listado de usuarios');
    }
    
    /*function segundaaccion() {
        $this->getModel()->set('titulo', 'Segunda Acción');
        $this->getModel()->set('twigFile', '_second.html');
    }*/
}