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

    abstract function __construct( $params = array() );


    /**
     * @param \fv\Http\Request $request
     * @return bool
     */
    abstract function canHandle( Request $request );

    /**
     * @abstract
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response
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
        $className = isset($config['class']) ? $config['class'] : "DefaultRoute";

        if( !strstr('\\', $className ))
            $className = __NAMESPACE__ . "\\" . $className;

        if( ! class_exists( $className ) )
            throw new RoutingException( "Route class {$className} not found" );

        $class = new $className( $config );

        if( ! $class instanceof AbstractRoute )
            throw new RoutingException( "{$className} not instance of fv\\Routing\\AbstractRoute: " );

        return $class;
    }


    /**
     * @param \fv\Http\Request $request
     *
     * @return \fv\Application\AbstractApplication
     * @throws \fv\Routing\Exception\RoutingException
     */
    final public function getApplicationFromRequest( Request $request ){
        $application = $request->internal->application;

        if( ! $application instanceof AbstractApplication )
            throw new RoutingException( "No application to show controller. What I have to do with this route?" );

        return $application;
    }
}
