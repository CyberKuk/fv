<?php
namespace Bundle\fv\MetroUIBundle\ViewModel\Component\ModuleGroup;
use \fv\ViewModel\ViewModel;

class Dashboard extends ViewModel{
    public $controller;

    function __construct( $controller ){
        $this->controller = $controller;
    }
}
