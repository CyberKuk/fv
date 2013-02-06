<?php
namespace Bundle\fv\MetroUIBundle\ViewModel\Component\AllModules;

use \fv\ViewModel\ViewModel;

class LeftBar extends ViewModel{
    public $controller;

    function __construct( $controller ){
//        var_dump( $controller );
        $this->controller = $controller;
    }
}
