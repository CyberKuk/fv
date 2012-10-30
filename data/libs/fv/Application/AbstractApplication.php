<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:30
 */

namespace fv\Application;

use fv\Http\Request;
use fv\Routing\Router;
use fv\Controller\ControllerLoader;
use fv\Layout\LayoutLoader;

abstract class AbstractApplication {

    /** @var \fv\Routing\Router */
    protected $router;

    private $path;
    private $namespace;
    private $loaded = false;

    function __construct( $config ) {
        $this->router = new Router;
        $this->path = rtrim( $config['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->namespace = rtrim( $config['namespace'], "\\") . "\\";
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
     * @param \fv\Routing\Router $router
     * @return \fv\Application\AbstractApplication
     */
    public function setRouter( Router $router ) {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \fv\Routing\Router
     */
    public function getRouter() {
        return $this->router;
    }


    /**
     * @return \fv\Controller\ControllerLoader
     */
    public function getControllerLoader(){
        return new ControllerLoader( $this );
    }

    /**
     * @return \fv\Layout\LayoutLoader
     */
    public function getLayoutLoader(){
        return new LayoutLoader( $this );
    }

}
