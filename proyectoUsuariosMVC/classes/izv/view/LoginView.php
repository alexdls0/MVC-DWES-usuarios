<?php

namespace izv\view;

use izv\model\Model;
use izv\tools\Util;
use izv\data\Usuario;
use izv\database\Database;
use izv\managedata\ManageUsuario;
use izv\tools\Reader;

class LoginView extends View {

    function __construct(Model $model) {
        parent::__construct($model);
        $this->getModel()->set('twigFolder', 'twigtemplates/twig');
        $this->getModel()->set('twigFile', '_login.html');
    }

    function render($accion) {
        $data = $this->getModel()->getViewData();
        $loader = new \Twig_Loader_Filesystem($data['twigFolder']);
        $twig = new \Twig_Environment($loader);
        return $twig->render($data['twigFile'], $data);
    }
}