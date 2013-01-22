<?php

namespace fv\Config;

use fv\Collection\Collection;
use fv\Parser\YamlParser;

class ConfigLoader {

    private static $extensionPriority = array("yml", "json", "php");

    public static function loadFile( $file ){
        $file = self::findFile($file);

        if( $file === false ) {
            throw new Exception\LoadConfigException("File '{$file}' not found");
        }

        $pathInfo = pathinfo($file);
        switch( $pathInfo['extension'] ){
            case 'json':
                $data = file_get_contents($file);
                return json_decode( $data, true );
            case 'yml':
                $parser = new YamlParser( $file );
                return $parser->parse();
            case 'php':
                /** @noinspection PhpIncludeInspection */
                return include $file;
            default:
                throw new Exception\LoadConfigException("Unknown file type {$pathInfo['extension']}");
        }
    }

    private static function findFile( $file ){
        if( file_exists( $file ) )
            return $file;

        foreach( self::$extensionPriority as $extension ){
            if( file_exists( $file . "." . $extension ) ){
                return $file . "." . $extension;
            }
        }

        return false;
    }

    public static function load( $name, $context = "/", $extends = true ){
        if( is_object($context) )
            $context = get_class($context);

        $collection = new Collection();
        $notFound = true;

        $currentContext = $context;

        while( $currentContext ){
            $path = ConfigRegister::getPath( $context );

            if( $path ){
                $file = self::findFile( $path . $name );

                if( $file !== false ){
                    $notFound = false;
                    $newCollection = new Collection( self::loadFile( $file ) );
                    $collection->merge($newCollection);
                }
            }

            if( ! $extends )
                break;

            $currentContext = get_parent_class($currentContext);
        }

        if( $notFound )
            throw new Exception\LoadConfigException("Config '{$name}' not found");

        return $collection;
    }

}
