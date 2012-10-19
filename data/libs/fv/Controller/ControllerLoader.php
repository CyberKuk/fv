<?php
/**
 * User: cah4a
 * Date: 12.10.12
 * Time: 16:22
 */

namespace fv\Controller;

use fv\Application\AbstractApplication;
use fv\Http\Request;
use fv\Controller\Exception\ControllerLoadException;

class ControllerLoader {

    /** @var AbstractApplication */
    private $application;

    public function __construct( AbstractApplication $application ){
        $this->setApplication( $application );
    }

    /**
     * @param                  $name
     * @param \fv\Http\Request $request
     *
     * @return AbstractController
     * @throws Exception\ControllerLoadException
     */
    public function createController( $name, Request $request ){
        $className = $this->getControllerClassName( $name );

        if( ! class_exists($className) ){
            throw new ControllerLoadException("Controller {$className} not found");
        }

        return new $className( $request );
    }

    public function controllerExist( $name ){
        $className = $this->getControllerClassName( $name );
        return class_exists($className);
    }

    private function getControllerClassName( $name ){
        $namespace = $this->getApplication()->getControllerNamespace();
        $className = $namespace . ucfirst($name) . "Controller";
        return $className;
    }

    /**
     * @param \fv\Application\AbstractApplication $application
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
