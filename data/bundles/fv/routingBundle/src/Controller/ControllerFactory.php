<?php
/**
 * User: cah4a
 * Date: 12.10.12
 * Time: 16:22
 */

namespace Bundle\fv\RoutingBundle\Controller;

use Bundle\fv\RoutingBundle\Controller\Exception\ControllerFactoryException;

class ControllerFactory {

    /** @var string */
    private $namespace;

    public function __construct( $namespace ){
        $this->setNamespace( $namespace );
    }

    /**
     * @param string $name
     *
     * @return AbstractController
     * @throws Exception\ControllerFactoryException
     */
    public function createController( $name ){
        $class = $this->getControllerClassName( $name );

        if( ! class_exists($class) ){
            throw new ControllerFactoryException("Controller {$class} not found");
        }

        $controller = new $class;

        if( ! $controller instanceof AbstractController )
            throw new ControllerFactoryException("Controller {$class} must be instance of " . __NAMESPACE__ . "\\AbstractController");

        return $controller;
    }

    public function controllerExist( $name ){
        $class = $this->getControllerClassName( $name );
        return class_exists($class);
    }

    protected function getControllerClassName( $name ){
        $class = $this->getNamespace() . ucfirst($name) . "Controller";
        return $class;
    }

    /**
     * @param string $namespace
     */
    final public function setNamespace($namespace) {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    final public function getNamespace() {
        return $this->namespace;
    }

}
