<?php

namespace fv\Config;

use fv\Collection;
use fv\Yaml\YamlParser;

class ConfigLoader {

    private static $extensionPriority = array("yml", "json", "php");

    static function loadArray( $file ){
        $path_info = pathinfo($file);

        if( empty( $path_info['extension'] ) || ! file_exists( $file ) ){
            foreach( self::$extensionPriority as $extension ){
                if( file_exists( $file . "." . $extension ) ){
                    $path_info['extension'] = $extension;
                    $file .= "." . $extension;
                    break;
                }
            }
        }

        if( !file_exists( $file ) ) {
            throw new Exception\LoadConfigException("File '{$file}' not found");
        }

        switch( $path_info['extension'] ){
            case 'json':
                $data = file_get_contents($file);
                return json_decode( $data, true );
            case 'yml':
                $data = file_get_contents($file);
                return YamlParser::parse( $data );
            case 'php':
                /** @noinspection PhpIncludeInspection */
                return include $file;
            default:
                throw new Exception\LoadConfigException("Unknown file type {$path_info['extension']}");
        }
    }

    static function loadCollection( $file ){
        return new Collection( self::loadArray($file) );
    }

}
