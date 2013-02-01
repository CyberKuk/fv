<?php

namespace Application\Frontend\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\RoutingBundle\Routing\Link;

class IndexController extends AbstractController {

    function getHello(){
        return "Hello";
    }

    function get( $name = "default world" ){
        var_dump( Link::to("backend:test", array("name" => 1)) );
        var_dump( Link::to("frontend:test", array("name" => 1)) );

        return array(
            'name' => $name
        );
    }

}
