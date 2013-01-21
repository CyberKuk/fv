<?php

namespace fv\Config;

use fv\Collection\Collection;
use fv\Parser\YamlParser;

class ConfigLoader {

    private static $extensionPriority = array("yml", "json", "php");

    public static function loadArray( $file ){
        $pathInfo = pathinfo($file);

        if( empty( $pathInfo['extension'] ) || ! file_exists( $file ) ){
            foreach( self::$extensionPriority as $extension ){
                if( file_exists( $file . "." . $extension ) ){
                    $pathInfo['extension'] = $extension;
                    $file .= "." . $extension;
                    break;
                }
            }
        }

        if( !file_exists( $file ) ) {
            throw new Exception\LoadConfigException("File '{$file}' not found");
        }

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

    static function load( $file ){
        return new Collection( self::loadArray($file) );
    }

}
