<?php

namespace Bundle\fv\RoutingBundle;

use Bundle\fv\RoutingBundle\Routing\Router;
use Bundle\fv\RoutingBundle\Routing\Link;
use fv\Connection\ConnectionFactory;
use fv\Http\Request;

class Kernel {

    /** @var \Bundle\fv\RoutingBundle\Routing\Router */
    protected $router;

    /** @var Request */
    private $request;

    /**
     * @param null|\fv\Http\Request $request
     */
    public function __construct( Request $request = null ) {
        $this->setRouter( Router::buildFromConfigFile('routes') );

        Link::setKernel( $this );

        if( is_null($request) )
            $request = Request::buildFromGlobal();

        $this->setRequest( $request );
    }

    /**
     * @return bool|\fv\Http\Response
     */
    public function handle(){
        return $this->getRouter()->handle( $this->getRequest() );
    }

    /**
     * @param Routing\Router $router
     * @return Kernel
     */
    final protected function setRouter( Router $router ) {
        $this->router = $router;
        return $this;
    }

    /**
     * @return Routing\Router
     */
    final public function getRouter() {
        return $this->router;
    }

    /**
     * @param $request
     * @return Kernel
     */
    final protected function setRequest($request) {
        $this->request = $request;
        return $this;
    }

    /**
     * @return \fv\Http\Request
     */
    final public function getRequest() {
        return $this->request;
    }


}
