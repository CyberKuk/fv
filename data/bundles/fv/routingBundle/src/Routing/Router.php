<?php

namespace Bundle\fv\RoutingBundle\Routing;

use fv\Http\Request;
use fv\Config\ConfigurableBuilder;
use fv\Config\ConfigLoader;
use Bundle\fv\RoutingBundle\Routing\Route\AbstractRoute;
use Bundle\fv\RoutingBundle\Routing\Exception\RouterException;

/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 17:24
 */
class Router {

    /** @var AbstractRoute[] */
    private $routes = array();

    /**
     * @param $key
     * @param AbstractRoute $route
     * @return $this
     */
    final public function addRoute( $key, AbstractRoute $route ){
        if( isset($this->routes[$key]) )
            throw new RouterException("Route with key {$key} duplicated.");

        $this->routes[$key] = $route;
        return $this;
    }

    /**
     * @param AbstractRoute[] $routes
     * @return $this
     */
    final public function addRoutes( array $routes ) {
        foreach( $routes as $key => $route ){
            $this->addRoute( $key, $route );
        }

        return $this;
    }

    final public function removeRoute( $key ){
        unset( $this->routes[$key] );

        return $this;
    }

    final public function getRoutes() {
        return $this->routes;
    }

    public function handle( Request $request ){
        foreach( $this->getRoutes() as $route ){
            $response = $route->handle( $request );

            if( $response === false || $response === null )
                continue;

            return $response;
        }

        throw new Exception\RouteNotFoundException("No route to handle request");
    }

    function loadFromCollection( $config ){
        $builder = \fv\Config\ConfigurableBuilder::createFromCollection( $config );

        $builder
            ->setDefaultNamespace(__NAMESPACE__ . "\\Route")
            ->setDefaultClass( "DefaultRoute" )
            ->setInstanceOf( __NAMESPACE__ . "\\Route\\AbstractRoute" )
            ->setPostfix("Route");

        $this->addRoutes( $builder->buildAll() );
    }

    function loadFromConfigFile( $file, $context = null ){
        $builder = ConfigurableBuilder::createFromFile( $file, $context );

        $builder
            ->setDefaultNamespace(__NAMESPACE__ . "\\Route")
            ->setDefaultClass( "DefaultRoute" )
            ->setInstanceOf( __NAMESPACE__ . "\\Route\\AbstractRoute" )
            ->setPostfix("Route");

        $this->addRoutes( $builder->buildAll() );
    }

}
