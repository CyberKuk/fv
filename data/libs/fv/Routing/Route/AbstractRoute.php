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

    abstract function __construct( \fv\Collection $params = null );


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
        $class = new static( $config );

        return $class;
    }
}
