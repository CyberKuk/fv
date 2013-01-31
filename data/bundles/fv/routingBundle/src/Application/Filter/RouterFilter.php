<?php

namespace Bundle\fv\RoutingBundle\Application\Filter;

use Bundle\fv\RoutingBundle\Routing\Router;

class RouterFilter extends AbstractFilter {

    /** @var Router */
    private $router;

    function __construct( Router $router ) {
        $this->setRouter($router);
    }

    /**
     * @param \Bundle\fv\RoutingBundle\Routing\Router $router
     * @return RouterFilter
     */
    public function setRouter( Router $router) {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }

    public function execute( FilterChain $chain ) {
        $response = $chain->execute();

        if( $response )
            return $response;

        return $this->getRouter()->handle( $chain->getRequest() );
    }

}
