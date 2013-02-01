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
use Bundle\fv\RoutingBundle\Application\Filter\RouterFilter;
use Bundle\fv\RoutingBundle\Application\Filter\FilterChain;
use Bundle\fv\RoutingBundle\Controller\ControllerFactory;
use Bundle\fv\RoutingBundle\Layout\LayoutFactory;

abstract class AbstractApplication {

    private $filterChain;
    private $path;
    private $namespace;
    private $loaded = false;
    private $router;

    public function __construct( Collection $config ) {
        $this->filterChain = new FilterChain();
        $this->path = rtrim( $config->path->get(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->namespace = rtrim( $config->namespace->get(), "\\") . "\\";
    }

    public function getPath(){
        return $this->path;
    }

    public function load(){
        if( ! $this->loaded ){
            $this->router = Router::buildFromCollection( ConfigLoader::load( 'routes', $this, false ) );
            $this->getFilterChain()->addFilter( new RouterFilter( $this->router ) );

            $this->loaded = true;
        }

        return $this;
    }

    /**
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response
     */
    final public function handle( Request $request ){
        $request->internal->application = $this;
        return $this->load()->getFilterChain()->setRequest( $request )->execute();
    }

    final public function getFilterChain() {
        return $this->filterChain;
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

    /**
     * @return Router
     */
    public function getRouter() {
        return $this->load()->router;
    }

}
