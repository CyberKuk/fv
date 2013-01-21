<?php

namespace fv\Storage;

use fv\Collection\Collection;

if( ! class_exists('\\Memcache') ){
    throw new \fv\Storage\Exception\StorageInstantiateException("Class \\Memcache is undefined. Is PHP_MEMCACHE extension enabled?");
}

class Memcache extends \Memcache implements Storage {

    private $host;
    private $port;
    private $timeout;

    private $init = false;

    private function __construct( $host = 'localhost', $port = '11211', $timeout = null ) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    private function init(){
        if( ! $this->init ){
            $this->init = true;
            $this->connect($this->host, $this->port, $this->timeout );
        }

        return $this;
    }

    final public function get( $key ) {
        $this->init();
    }

    final public function set( $key, $value) {
        $this->init();
    }

    public static function build( Collection $config) {
        static $instance;

        if( empty($instance) ){
            $instance = new static( $config->host->get(), $config->port->get(), $config->timeout->get() );
        }

        return $instance;
    }


}
