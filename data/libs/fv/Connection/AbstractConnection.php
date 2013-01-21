<?php

namespace fv\Connection;

abstract class AbstractConnection {

    private $driver;
    private $schema;
    private $isConnected;

    final public function __construct( array $schema ){
        $this->setSchema( $schema );
    }

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

    public function getSchema() {
        return $this->schema;
    }

}
