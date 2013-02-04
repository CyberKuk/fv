<?php

namespace fv\View;

use fv\View\Exception\ViewBuilderException as Exception;

class ViewBuilder {

    static private $engines = array(
        "twig" => "Twig"
    );
    static $postfixes = array(
        "Layout",
        "Controller",
        "Component"
    );

    static function build( $class ){
        $view = null;
        $className = null;

        if( is_string( $class ) )
            $className = $class;

        if( is_object($class) )
            $className = get_class($class);

        $currentClassName = $className;
        while( ! $view && $currentClassName ){
            $paths = TemplateRegister::getPaths( $currentClassName );

            if( ! empty($paths) ){
                foreach( $paths as $namespace => $path ){
                    $tailNamespace = preg_replace( "/^" . preg_quote( $namespace , "/") . "/", "", $currentClassName );

                    foreach( self::$postfixes as $postfix ){
                        $tailNamespace = preg_replace( "/" . preg_quote($postfix, "/") . "$/", "", $tailNamespace, 1, $count );

                        if( $count > 0 )
                            break;
                    }

                    $tail = preg_replace_callback( "/(\\\|^)(\w)/", function( $match ){
                        return DIRECTORY_SEPARATOR . strtolower( $match[2] );
                    }, $tailNamespace );

                    $tail = preg_replace_callback( "/([A-Z])/", function( $match ){
                        return "-" . strtolower( $match[1] );
                    }, $tail );

                    $path = trim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . trim( $tail, DIRECTORY_SEPARATOR );

                    foreach( self::$engines as $ext => $engine ){
                        if( file_exists( "{$path}.{$ext}" ) ){
                            /** @var $view AbstractView */
                            $engine = __NAMESPACE__ . "\\" . $engine;
                            $view = new $engine;
                            $view->setTemplate( "{$path}.{$ext}" );
                            break 2;
                        }
                    }
                }
            }

            $currentClassName = get_parent_class( $currentClassName );
        }

        if( !$view )
            throw new Exception("Template for {$className} not found");

        return $view;
    }

}
