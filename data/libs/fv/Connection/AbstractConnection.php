<?php

namespace fv\Connection;

use fv\Collection\Collection;

abstract class AbstractConnection {

    private $driver;
    private $schema;
    private $isConnected;

    /**
     * @return mixed driver
     */
    abstract protected function connect();

    private function setDriver( $driver ) {
        $this->driver = $driver;
        return $this;
    }

    public function getDriver() {
        if( !$this->isConnected() ){
            $this->setDriver( $this->connect() );
            $this->isConnected = true;
        }

        return $this->driver;
    }

    public function isConnected() {
        return $this->isConnected;
    }

    public function setSchema( $schema ) {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return \fv\Collection\Collection
     */
    public function getSchema() {
        return $this->schema;
    }

    public static function build( Collection $schema ){
        return (new static)->setSchema( $schema );
    }

}
