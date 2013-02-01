<?php
/**
 * User: cah4a
 * Date: 11.10.12
 * Time: 13:08
 */

namespace Bundle\fv\RoutingBundle\Routing\Route;

use fv\Http\Request;
use fv\Collection\Collection;

use Bundle\fv\RoutingBundle\Application\AbstractApplication;
use Bundle\fv\RoutingBundle\Routing\Exception\RoutingException;

class UriBasedControllerRoute extends AbstractRoute {

    public function __construct( Collection $params = null ) {}

    public function handle( Request $request ){
        $application = null;

        if( $request->internal->application )
            $application = $request->internal->application->get();

        if( ! $application instanceof AbstractApplication )
            throw new RoutingException( "No application to show controller. What I have to do with this route?" );

        $controllerFactory = $application->getControllerFactory();

        $controllerName = str_replace( '/', "\\", trim($request->getUri(), "/") );
        $controller = null;

        if( empty($controllerName) )
            $controllerName = "Index";

        if( $controllerFactory->controllerExist( $controllerName ) ){
            $controller = $controllerFactory->createController( $controllerName );
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

    public function createLink(array $params = null) {
        if( count($params) != 1 ){
            throw new \Bundle\fv\RoutingBundle\Routing\Exception\CreateLinkException("Create link must be ");
        }
    }

}
