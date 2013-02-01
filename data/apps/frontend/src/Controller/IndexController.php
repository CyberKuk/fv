<?php

namespace Application\Frontend\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\RoutingBundle\Routing\Link;

class IndexController extends AbstractController {

    function getHello(){
        return "Hello";
    }

    function get( $name = "default world" ){
        return array(
            'name' => $name
        );
    }

}
