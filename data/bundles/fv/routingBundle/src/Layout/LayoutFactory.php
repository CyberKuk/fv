<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:04
 */

namespace Bundle\fv\RoutingBundle\Layout;

use fv\Http\Request;
use Bundle\fv\RoutingBundle\Layout\Exception\LayoutFactoryException;

class LayoutFactory {

    /**
     * @var string
     */
    private $namespace;

    function __construct( $namespace ) {
        $this->setNamespace( $namespace );
    }

    function createLayout( Request $request ){
        $layout = null;

        if( $request->isXmlHttp() ){
            $class = $this->getNamespace() . "AjaxLayout";

            if( class_exists( $class ) )
                $layout = new $class;
        }

        if( empty($layout) ){
            $class = $this->getNamespace() . "DefaultLayout";

            if( class_exists($class) )
                $layout = new $class;
            else
                throw new LayoutFactoryException("Layout {$class} not found");
        }

        if( ! $layout instanceof AbstractLayout ){
            throw new LayoutFactoryException("Layout " . get_class($layout) . " must be instance of " . __NAMESPACE__ . "\\AbstractLayout");
        }

        return $layout;
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
