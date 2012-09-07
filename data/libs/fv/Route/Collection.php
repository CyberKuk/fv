<?php

namespace fv;

/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 17:24
 */
class Route_Collection extends Route {

    /** @var Route[] */
    private $routes = array();

    /**
     * @param $key
     * @param Route $route
     * @return $this
     */
    final public function addRoute( $key, Route $route ){
        $this->routes[$key] = $route;
        return $this;
    }

    /**
     * @param Route[] $routes
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
            if( $response === false )
                continue;

            return $response;
        }

        return false;
    }

}
