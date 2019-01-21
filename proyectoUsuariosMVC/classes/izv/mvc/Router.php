<?php

namespace izv\mvc;

class Router {

    private $rutas, $ruta;
    
    function __construct($ruta) {
        $this->rutas = array(
            'index' => new Route('IndexModel', 'IndexView', 'IndexController'),
            'login' => new Route('LoginModel', 'LoginView', 'LoginController'),
            'sign' => new Route('SignModel', 'SignView', 'SigninController'),
            'user' => new Route('UserModel', 'UserView', 'UserController'),
            'edituser' => new Route('EditUserModel', 'EditUserView', 'EditUserController'),
            'admin' => new Route('AdminModel', 'AdminView', 'AdminController'),
            'editadmin' => new Route('EditAdminModel', 'EditAdminView', 'EditAdminController'),
            'users' => new Route('UsersModel', 'UsersView', 'UsersController'),
            'edituserfromadmin' => new Route('EditUFAModel', 'EditUFAView', 'EditUFAController')
        );
        $this->ruta = $ruta;
    }

    function getRoute() {
        $ruta = $this->rutas['index'];
        if(isset($this->rutas[$this->ruta])) {
            $ruta = $this->rutas[$this->ruta];
        }
        return $ruta;
    }
}