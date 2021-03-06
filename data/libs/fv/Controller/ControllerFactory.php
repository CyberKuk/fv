<?php
/**
 * User: cah4a
 * Date: 12.10.12
 * Time: 16:22
 */

namespace fv\Controller;

use fv\Application\AbstractApplication as Application;
use fv\Controller\Exception\ControllerFactoryException;

class ControllerFactory {

    /** @var Application */
    private $application;

    public function __construct( Application $application ){
        $this->setApplication( $application );
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
            throw new ControllerFactoryException("Controller {$class} must be instance of \\fv\\Controller\\AbstractController");

        return $controller;
    }

    public function controllerExist( $name ){
        $class = $this->getControllerClassName( $name );
        return class_exists($class);
    }

    private function getControllerClassName( $name ){
        $namespace = $this->getApplication()->getControllerNamespace();
        $class = $namespace . ucfirst($name) . "Controller";
        return $class;
    }

    /**
     * @param \fv\Application\AbstractApplication $application
     * @return \fv\Controller\Factory
     */
    private function setApplication( $application ) {
        $this->application = $application;
        return $this;
    }

    /**
     * @return \fv\Application\AbstractApplication
     */
    private function getApplication() {
        return $this->application;
    }

}
