<?php

namespace fv\Storage;

use fv\Collection\Collection;

if( ! class_exists('\\Memcache') ){
    throw new \fv\Storage\Exception\StorageInstantiateException("Class \\Memcache is undefined. Is PHP_MEMCACHE extension enabled?");
}

/** @noinspection PhpDocSignatureInspection */
class Memcache implements Storage {

    private $host;
    private $port;
    private $timeout;
    private $connection;

    private function __construct( $host = 'localhost', $port = '11211', $timeout = null ) {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->connection = new \Memcache();
        $this->connection->connect($this->host, $this->port, $this->timeout );
    }

    final public function get( $key ) {
        return $this->connection->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    final public function set( $key, $value ) {
        $timeout = func_get_arg(2);
        return $this->connection->set($key, $value, null, $timeout);
    }

    final public function delete( $key ) {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->connection->delete($key);
    }

    public static function build( Collection $config) {
        static $instance;

        if( empty($instance) ){
            $instance = new static( $config->host->get(), $config->port->get(), $config->timeout->get() );
        }

        return $instance;
    }
}
