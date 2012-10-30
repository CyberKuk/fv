<?php
/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 17:38
 */

namespace fv\Config;

class ConfigLoader {

    static function load( $file ){
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

}
