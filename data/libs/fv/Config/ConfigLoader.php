<?php
/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 17:38
 */

namespace fv\Config;
use fv\Collection;

class ConfigLoader {

    static function loadArray( $file ){
        $path_info = pathinfo($file);
        switch( $path_info['extension'] ){
            case 'json':
                $data = file_get_contents($file);
                return json_decode( $data, true );
            case 'php':
                /** @noinspection PhpIncludeInspection */
                return include $file;
            default:
                throw new Exception\LoadConfigException("");
        }
    }

    static function loadCollection( $file ){
        return new Collection( self::loadArray($file) );
    }

}
