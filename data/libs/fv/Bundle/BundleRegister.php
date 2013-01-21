<?php

namespace fv\Bundle;

use ClassLoader\Register;
use fv\View\TemplateRegister;
use fv\Bundle\Exception\BundleRegisterException;

class BundleRegister {

    public static function register( $namespace, $path = null ){
        static $bundles = array();

        if( isset( $bundles[$namespace] ) )
            throw new BundleRegisterException("Bundle {$namespace} already registred");

        if( is_null($path) )
            $path = "bundles/fv/" . lcfirst($namespace);

        Register::createLoader( $namespace, $path . "/src" );

        $bundleClassName = "{$namespace}\\Bundle";

        if( ! class_exists($bundleClassName) )
            throw new BundleRegisterException("Bundle Class {$bundleClassName} not exist");

        if( ! is_subclass_of($bundleClassName, "fv\\Bundle\\AbstractBundle") )
            throw new BundleRegisterException("Bundle Class {$bundleClassName} must be instance of fv\\Bundle\\AbstractBundle");

        /** @var $bundleClass AbstractBundle */
        $bundleClass = new $bundleClassName();

        foreach( $bundleClass->getDependencies() as $dependentNamespace ){
            if( !isset($bundles[$dependentNamespace]) )
                throw new BundleRegisterException("Bundle {$namespace} depend on {$dependentNamespace}, which not include");
        }

        TemplateRegister::registerNamespace( $namespace, $path );
    }

}
