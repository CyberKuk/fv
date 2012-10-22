<?php
/**
 * User: cah4a
 * Date: 11.10.12
 * Time: 13:08
 */

namespace fv\Routing\Route;

use fv\Http\Request;
use fv\Application\AbstractApplication;
use fv\Controller\AbstractController;
use fv\Controller\ControllerLoader;
use fv\Routing\Exception\RoutingException;

use fv\Viewlet;

class UriBasedControllerRoute extends AbstractRoute {

    function __construct( $params = array() ) {

    }

    public function handle( Request $request ){
        $application = $request->internal->application;

        if( ! $application instanceof AbstractApplication )
            throw new RoutingException( "No application to show controller. What I have to do with this route?" );

        $controllerLoader = new ControllerLoader( $application );

        $controllerName = str_replace( '/', "\\", trim($request->getUri(), "/") );
        $controller = null;

        if( !empty($controllerName) ){
            if( $controllerLoader->controllerExist( $controllerName ) ){
                $controller = $controllerLoader->createController( $controllerName );
            } else {
                $controllerName .= "\\Index";
                if( $controllerLoader->controllerExist( $controllerName ) ){
                    $controller = $controllerLoader->createController( $controllerName );
                }
            }
        } elseif( $controllerLoader->controllerExist( "Index" ) ){
            $controller = $controllerLoader->createController( "Index" );
        }

        if( $controller instanceof AbstractController ){
            $controller
                ->setRequest( $request )
                ->execute();

            $layout = $application->getLayoutLoader()->createLayout( $request );
            $layout
                ->setResponse( $controller->getResponse() )
                ->execute();

            return $layout->getResponse();
        }

        return false;
    }



}
