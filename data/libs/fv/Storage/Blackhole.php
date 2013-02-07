<?php
namespace fv\Storage;

use fv\Collection\Collection;

class Blackhole implements Storage {

    public static function build( Collection $config) {
        static $instance;

        if( empty($instance) ){
            $instance = new static();
        }

        return $instance;
    }

    function get( $key ){
        return null;
    }

    public function delete($key)
    {
        return null;
    }


    function set( $key, $value ){
        return true;
    }

}
