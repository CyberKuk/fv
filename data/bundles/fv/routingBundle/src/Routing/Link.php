<?php

namespace Bundle\fv\RoutingBundle\Routing;

use Bundle\fv\RoutingBundle\Application\ApplicationBuilder;
use Bundle\fv\RoutingBundle\Application\AbstractApplication;
use Bundle\fv\RoutingBundle\Routing\Exception\CreateLinkException;
use Bundle\fv\RoutingBundle\Application\Exception\ApplicationLoadException;

class Link {

    static function to( $app, $route, array $params = null ) {
        if( is_string($app) ){
            $appsFactory = new ApplicationBuilder;
            try {
                $app = $appsFactory->build($app);
            } catch( ApplicationLoadException $exception ){
                throw new CreateLinkException("Can't find application {$app}");
            }
        }

        if( ! $app instanceof AbstractApplication ){
            throw new CreateLinkException("Unexpected app parameter type");
        }

        $route = $app->getRouter()->getRoute( $route );
        if( ! $route ){
            throw new CreateLinkException("Can't find route {$route} in " . get_class($app));
        }

        return $route->createLink( $params );
    }

}
