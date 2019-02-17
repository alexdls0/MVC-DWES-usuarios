<?php

namespace izv\view;

use izv\model\Model;
use izv\tools\Util;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;
use izv\mvc\FrontController;

class AdminView extends View {

    function __construct(Model $model) {
        parent::__construct($model);
    }
    
    function render($accion) {
        $data = $this->getModel()->getViewData();
        $loader = new \Twig_Loader_Filesystem($data['twigFolder']);
        $twig = new \Twig_Environment($loader);
        return $twig->render($data['twigFile'], $data);
    }
}