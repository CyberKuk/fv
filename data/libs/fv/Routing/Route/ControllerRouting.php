<?php
/**
 * User: cah4a
 * Date: 11.10.12
 * Time: 13:08
 */

namespace fv\Routing\Route;

use fv\Http\Request;
use fv\Controller\AbstractController;
use fv\Controller\ControllerLoader;
use fv\Routing\Exception\RoutingException;

use fv\Viewlet;

class ControllerRouting extends AbstractRoute {

    /** @var AbstractController */
    private $controller;

    function __construct( $params = array() ) {

    }

    public function canHandle( Request $request ){
        $application = $this->getApplicationFromRequest( $request );
        $controllerLoader = new ControllerLoader( $application );

        $controllerName = str_replace( '/', "\\", trim($request->getUri(), "/") );

        if( !empty($controllerName) ){
            if( $controllerLoader->controllerExist( $controllerName ) ){
                $this->controller = $controllerLoader->createController( $controllerName, $request );
                return true;
            }

            $controllerName .= "\\Index";
            if( $controllerLoader->controllerExist( $controllerName ) ){
                $this->controller = $controllerLoader->createController( $controllerName, $request );
                return true;
            }
        } elseif( $controllerLoader->controllerExist( "Index" ) ){
            $this->controller = $controllerLoader->createController( "Index", $request );
            return true;
        }

        return false;
    }

    public function handle( Request $request ) {
        if( ! $this->controller instanceof AbstractController ){
            throw new RoutingException("No controller to handle route");
        }

        $this->controller->execute();

        $this->controller->getTemplateName();

        return $this->controller->getResponse();
    }

}
