<?php

namespace fv\Config;

class ConfigRegister {

    private static $namespaces = array();
    private static $isSort = true;

    public static function registerNamespace( $namespace, $path ){
        self::$namespaces[$namespace] = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        self::$isSort = false;
    }

    public static function unregisterNamespace( $namespace ){
        unset( self::$namespaces[$namespace] );
    }

    public static function getPath( $class ){
        foreach( self::getNamespaces() as $namespace => $path ){
            if( preg_match(  "/^" .  preg_quote( $namespace, "/") . "/", $class ) )
                return $path;
        }

        return false;
    }

    private static function getNamespaces(){
        self::$isSort = true;

        uksort(self::$namespaces, function( $n1, $n2 ){
            return strlen($n1) < strlen($n2);
        });

        return self::$namespaces;
    }
}
