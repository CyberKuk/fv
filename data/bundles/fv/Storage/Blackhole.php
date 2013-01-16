<?php
namespace Bundle\fv\Storage;

class Blackhole implements Storage {

    public static function build(\fv\Collection $config) {
        static $instance;

        if( empty($instance) ){
            $instance = new static();
        }

        return $instance;
    }

    function get( $key ){
        return null;
    }

    function set( $key, $value ){
        return true;
    }

}
