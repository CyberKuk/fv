<?php

namespace fv\Entity\Query;

use fv\Connection\AbstractConnection;
use fv\Entity\AbstractEntity;

abstract class AbstractQuery {

    /** @var string Entity Class Name */
    private $entity;

    /** @var AbstractConnection */
    private $connection;

    abstract public function fetch( $key );
    abstract public function persist( AbstractEntity $entity );
    abstract public function remove( AbstractEntity $entity );

    /**
     * @param \fv\Connection\AbstractConnection $connection
     */
    final public function __construct( AbstractConnection $connection ){
        $this->setConnection( $connection );
    }

    /**
     * @param string $entity Entity Class Name
     *
     * @return AbstractQuery
     */
    public function setEntity( $entity ) {
        $this->entity = (string)$entity;
        return $this;
    }

    /**
     * @return string Entity Class Name
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * @param \fv\Connection\AbstractConnection $connection
     * @return \fv\Entity\Query\AbstractQuery
     */
    private function setConnection( AbstractConnection $connection ) {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return \fv\Connection\AbstractConnection
     */
    public function getConnection() {
        return $this->connection;
    }

    public function getSchema(){
        return \fv\Entity\EntitySchema::getSchema( $this->getEntity() );
    }

}
