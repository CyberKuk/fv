<?php

namespace fv\Routing\Route;

use fv\Http\Request;
use fv\Http\Response;

use fv\Routing\Exception\RoutingException;

use fv\Application\AbstractApplication;

/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 17:28
 */
abstract class AbstractRoute {

    abstract function __construct( array $params = array() );


    /**
     * @abstract
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response | bool(false) if route can't handle Request
     * @throws \fv\Routing\Exception\RouteNotFoundException
     */
    abstract public function handle( Request $request );

    /**
     * @param $config
     *
     * @return AbstractRoute
     * @throws \fv\Routing\Exception\RoutingException
     */
    final static public function build( $config ){
        $className = isset($config['class']) ? $config['class'] : "Default";
        $className = $className . "Route";

        if( !strstr('\\', $className ))
            $className = __NAMESPACE__ . "\\" . $className;

        if( ! class_exists( $className ) )
            throw new RoutingException( "Route class {$className} not found" );

        $class = new $className( $config );

        if( ! $class instanceof AbstractRoute )
            throw new RoutingException( "{$className} not instance of fv\\Routing\\AbstractRoute" );

        return $class;
    }
}
