<?php
/**
 * User: cah4a
 * Date: 11.10.12
 * Time: 13:08
 */

namespace Bundle\fv\RoutingBundle\Routing\Route;

use fv\Http\Request;
use Bundle\fv\RoutingBundle\Application\AbstractApplication;
use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\RoutingBundle\Controller\ControllerFactory;
use Bundle\fv\RoutingBundle\Routing\Exception\RoutingException;

use fv\Viewlet;
use fv\Collection\Collection;

class UriBasedControllerRoute extends AbstractRoute {

    function __construct( Collection $params = null ) {

    }

    public function handle( Request $request ){
        $application = null;

        if( $request->internal->application )
            $application = $request->internal->application->get();

        if( ! $application instanceof AbstractApplication )
            throw new RoutingException( "No application to show controller. What I have to do with this route?" );

        $controllerFactory = new ControllerFactory( $application );

        $controllerName = str_replace( '/', "\\", trim($request->getUri(), "/") );
        $controller = null;

        if( !empty($controllerName) ){
            if( $controllerFactory->controllerExist( $controllerName ) ){
                $controller = $controllerFactory->createController( $controllerName );
            } else {
                $controllerName .= "\\Index";
                if( $controllerFactory->controllerExist( $controllerName ) ){
                    $controller = $controllerFactory->createController( $controllerName );
                }
            }
        } elseif( $controllerFactory->controllerExist( "Index" ) ){
            $controller = $controllerFactory->createController( "Index" );
        }

        if( $controller instanceof AbstractController ){
            $controller
                ->setRequest( $request )
                ->execute();

            $layout = $application->getLayoutFactory()->createLayout( $request );
            $layout
                ->setResponse( $controller->getResponse() )
                ->execute();

            return $layout->getResponse();
        }

        return false;
    }



}
