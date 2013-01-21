<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:04
 */

namespace RoutingBundle\Layout;

use RoutingBundle\Application\AbstractApplication as Application;
use fv\Http\Request;

use RoutingBundle\Layout\Exception\LayoutFactoryException;

class LayoutFactory {

    /**
     * @var \RoutingBundle\Application\AbstractApplication
     */
    private $application;

    function __construct( Application $application ) {
        $this->setApplication( $application );
    }

    function createLayout( Request $request ){
        $namespace = $this->getApplication()->getLayoutNamespace();

        $layout = null;

        if( $request->isXmlHttp() ){
            $class = $namespace . "AjaxLayout";

            if( class_exists( $class ) )
                $layout = new $class;
        }

        if( empty($layout) ){
            $class = $namespace . "DefaultLayout";

            if( class_exists($class) )
                $layout = new $class;
            else
                throw new LayoutFactoryException("Layout {$class} not found");
        }

        if( ! $layout instanceof AbstractLayout ){
            throw new LayoutFactoryException("Layout " . get_class($layout) . " must be instance of \\fv\\Layout\\AbstractLayout");
        }

        return $layout;
    }

    /**
     * @param \RoutingBundle\Application\AbstractApplication $application
     * @return \RoutingBundle\Layout\LayoutFactory
     */
    private function setApplication( $application ) {
        $this->application = $application;
        return $this;
    }

    /**
     * @return \RoutingBundle\Application\AbstractApplication
     */
    private function getApplication() {
        return $this->application;
    }

}
