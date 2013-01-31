<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:30
 */

namespace Bundle\fv\RoutingBundle\Application;

use fv\Http\Request;
use fv\Config\ConfigLoader;
use fv\Collection\Collection;
use Bundle\fv\RoutingBundle\Routing\Router;
use Bundle\fv\RoutingBundle\Controller\ControllerFactory;
use Bundle\fv\RoutingBundle\Layout\LayoutFactory;

abstract class AbstractApplication {

    /** @var \Bundle\fv\RoutingBundle\Routing\Router */
    protected $router;

    private $path;
    private $namespace;
    private $loaded = false;

    public function __construct( Collection $config ) {
        $this->router = new Router;
        $this->path = rtrim( $config->path->get(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->namespace = rtrim( $config->namespace->get(), "\\") . "\\";
    }

    public function getPath(){
        return $this->path;
    }

    public function load(){
        $this->router->loadFromCollection( ConfigLoader::load( 'routes', $this, false ) );
        $this->loaded = true;
    }

    /**
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response
     */
    final public function handle( Request $request ){
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
     * @param \Bundle\fv\RoutingBundle\Routing\Router $router
     * @return \Bundle\fv\RoutingBundle\Application\AbstractApplication
     */
    public function setRouter( Router $router ) {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }


    /**
     * @return \Bundle\fv\RoutingBundle\Controller\ControllerFactory
     */
    public function getControllerFactory(){
        return new ControllerFactory( $this );
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Layout\LayoutFactory
     */
    public function getLayoutFactory(){
        return new LayoutFactory( $this );
    }

}
