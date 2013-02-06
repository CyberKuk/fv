<?php
namespace Bundle\fv\MetroUIBundle\ViewModel\Module\AbstractModule;

use \fv\ViewModel\ViewModel;

class LeftBar extends ViewModel{
    public $controller;

    function __construct( $controller ){
        $this->controller = $controller;
    }
}
