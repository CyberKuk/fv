<?php

namespace fv\Bundle;

use ClassLoader\Register as ClassLoaderRegister;
use fv\View\TemplateRegister;
use fv\Bundle\Exception\BundleRegisterException;

class BundleRegister {

    public static $bundles = [];

    public static function register( $namespace, $path = null ){
        $namespace = trim( $namespace, "\\");

        if( isset( self::$bundles[$namespace] ) )
            throw new BundleRegisterException("Bundle {$namespace} already registered");

        if( is_null($path) ){
            $path = preg_replace( "/Bundle\\\/", "", $namespace);

            $path = preg_replace_callback('/\\\([A-Z])/', function( $matches ){
                return DIRECTORY_SEPARATOR . lcfirst( $matches[1] );
            }, lcfirst( $path ));

            $path = "bundles" . DIRECTORY_SEPARATOR . $path;
        }

        ClassLoaderRegister::createLoader( $namespace, $path . DIRECTORY_SEPARATOR . "src" );

        $bundleClassName = "{$namespace}\\Bundle";

        if( ! class_exists($bundleClassName) )
            throw new BundleRegisterException("Bundle Class {$bundleClassName} not exist");

        if( ! is_subclass_of($bundleClassName, "fv\\Bundle\\AbstractBundle") )
            throw new BundleRegisterException("Bundle Class {$bundleClassName} must be instance of fv\\Bundle\\AbstractBundle");

        /** @var $bundle AbstractBundle */
        $bundle = new $bundleClassName();

        foreach( $bundle->getDependencies() as $dependentNamespace ){
            if( !isset(self::$bundles[$dependentNamespace]) )
                throw new BundleRegisterException("Bundle {$namespace} depend on {$dependentNamespace}, which not include");
        }

        if( is_dir($path . DIRECTORY_SEPARATOR . "views") )
            TemplateRegister::registerNamespace( $namespace, $path . DIRECTORY_SEPARATOR . "views" );

        if( is_dir($path . DIRECTORY_SEPARATOR . "configs") )
            \fv\Config\ConfigRegister::registerNamespace( $namespace, $path . DIRECTORY_SEPARATOR . "configs" );

        self::$bundles[$namespace] = $bundle;
    }

}
