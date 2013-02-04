<?php

namespace Bundle\fv\RoutingBundle\Routing;

use Bundle\fv\RoutingBundle\Application\ApplicationBuilder;
use Bundle\fv\RoutingBundle\Routing\Route\PrefixRoute;
use Bundle\fv\RoutingBundle\Routing\Exception\CreateLinkException;
use Bundle\fv\RoutingBundle\Application\Exception\ApplicationLoadException;

class Link {

    /** @var \Bundle\fv\RoutingBundle\Kernel */
    private static $kernel;

    static function to( $path, array $params = null ) {
        $paths = explode(":", $path);

        $appName = $paths[0];
        $appRoute = isset($paths[1]) ? $paths[1] : null;

        $route = null;
        foreach( self::$kernel->getRouter()->getRoutes() as $key => $kernelRoute ){
            if( $kernelRoute instanceof PrefixRoute ){
                if( $key == $appName || $kernelRoute->getApplicationName() == $appName ){
                    $route = $kernelRoute;
                    break;
                }
            }
        }

        if( ! $route instanceof PrefixRoute )
            throw new CreateLinkException("{$path} can't resolve to create link");

        $prefix = $route->createLink($params);

        if( ! $appRoute )
            return $prefix;

        $appsBuilder = new ApplicationBuilder;
        try {
            $app = $appsBuilder->build( $route->getApplicationName() );
        } catch( ApplicationLoadException $exception ){
            throw new CreateLinkException("Can't find application {$appName}");
        }

        $appRoute = $app->getRouter()->getRoute( $appRoute );
        if( ! $appRoute ){
            throw new CreateLinkException("Can't find route '{$appRoute}' in " . get_class($app));
        }

        return rtrim( $prefix, "/" ) . "/" . ltrim( $appRoute->createLink( $params ), "/");
    }

    public static function setKernel($kernel) {
        if( isset(self::$kernel) )
            throw new CreateLinkException("Kernel already assigned");

        self::$kernel = $kernel;
    }

}
