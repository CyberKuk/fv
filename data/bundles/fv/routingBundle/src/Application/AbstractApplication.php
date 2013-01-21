<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:30
 */

namespace RoutingBundle\Application;

use fv\Http\Request;
use fv\Collection\Collection;
use RoutingBundle\Routing\Router;
use RoutingBundle\Controller\ControllerFactory;
use RoutingBundle\Layout\LayoutFactory;

abstract class AbstractApplication {

    /** @var \RoutingBundle\Routing\Router */
    protected $router;

    private $path;
    private $namespace;
    private $loaded = false;

    function __construct( Collection $config ) {
        $this->router = new Router;
        $this->path = rtrim( $config->path->get(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->namespace = rtrim( $config->namespace->get(), "\\") . "\\";
    }

    public function getPath(){
        return $this->path;
    }

    public function load(){
        $this->router->loadFromConfigFile( $this->getPath() . 'configs/routes.json' );
        $this->loaded = true;
    }

    /**
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response
     */
    final function handle( Request $request ){
        if( ! $this->loaded )
            $this->load();

        $request->internal->application = $this;

        return $this->getRouter()->handle( $request );
    }

    public function getNamespace(){
        return $this->namespace;
    }

    public function getControllerNamespace(){
        return $this->getNamespace() . "Controller\\";
    }

    public function getLayoutNamespace(){
        return $this->getNamespace() . "Layout\\";
    }

    /**
     * @param \RoutingBundle\Routing\Router $router
     * @return \RoutingBundle\Application\AbstractApplication
     */
    public function setRouter( Router $router ) {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \RoutingBundle\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }


    /**
     * @return \RoutingBundle\Controller\ControllerFactory
     */
    public function getControllerFactory(){
        return new ControllerFactory( $this );
    }

    /**
     * @return \RoutingBundle\Layout\LayoutFactory
     */
    public function getLayoutFactory(){
        return new LayoutFactory( $this );
    }

}
