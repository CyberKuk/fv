<?php

namespace Bundle\fv\RoutingBundle;

use Bundle\fv\RoutingBundle\Routing\Router;
use fv\Connection\ConnectionFactory;
use fv\Http\Request;

class Kernel {

    /** @var \Bundle\fv\RoutingBundle\Routing\Router */
    protected $router;

    function __construct() {
        $this->setRouter( Router::buildFromConfigFile('routes') );
    }

    public function handle(){
        return $this->getRouter()->handle( Request::buildFromGlobal() );
    }

    final protected function setRouter( Router $router ) {
        $this->router = $router;
        return $this;
    }

    public function getRouter() {
        return $this->router;
    }
}
