<?php

namespace fv\Routing;

use fv\Http\Request;
use fv\Config\ConfigLoader;
use fv\Routing\Route\AbstractRoute;

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

    function loadFromConfigFile( $file ){
        $routes = ConfigLoader::load( $file );

        foreach( $routes as $key => $route ){
            $class = AbstractRoute::build( $route );
            $this->addRoute( $key, $class );
        }
    }

}
