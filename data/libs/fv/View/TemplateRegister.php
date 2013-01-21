<?php

namespace fv\View;

class TemplateRegister {

    private static $namespaces = array();

    public static function registerNamespace( $namespace, $path ){
        self::$namespaces[$namespace] = $path;
    }

    public static function unregisterNamespace( $namespace ){
        unset( self::$namespaces[$namespace] );
    }

    public static function getPaths( $class ){
        $result = array();

        foreach( self::$namespaces as $namespace => $path ){
            if( preg_match(  "/^" .  preg_quote( $namespace, "/") . "/", $class ) )
                $result[$namespace] = $path;
        }

        return $result;
    }

}
