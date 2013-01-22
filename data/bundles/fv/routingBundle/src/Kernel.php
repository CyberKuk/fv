<?php
/**
 * User: cah4a
 * Date: 10.09.12
 * Time: 18:47
 */

namespace RoutingBundle;

use RoutingBundle\Routing\Router;

use RoutingBundle\Application\ApplicationFactory;
use fv\Connection\ConnectionFactory;

use fv\Http\Request;

class Kernel {

    /** @var \RoutingBundle\Routing\Router */
    protected $router;

    /** @var \RoutingBundle\Application\ApplicationFactory */
    protected $applicationFactory;

    /** @var \fv\Connection\ConnectionFactory */
    protected $connectionFactory;

    private $loaded = false;

    function __construct() {
        $this->router = new Router;
        $this->applicationFactory = new ApplicationFactory;
        $this->connectionFactory = new ConnectionFactory;
    }

    public function load(){
        $this->router->loadFromConfigFile( 'routes' );
        $this->applicationFactory->loadFromConfigFile( 'applications' );
        $this->connectionFactory->loadFromConfigFile( 'connections' );

        $this->loaded = true;
    }

    public function handle(){
        if( !$this->loaded )
            $this->load();

        $request = Request::buildFromGlobal();
        $request->internal->kernel = $this;

        return $this->getRouter()->handle( $request );
    }

    public function setRouter( $router ) {
        $this->router = $router;
        return $this;
    }

    public function getRouter() {
        return $this->router;
    }
}
