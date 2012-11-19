<?php

namespace fv\Entity\Query;

use fv\Connection\AbstractConnection;
use fv\Entity\AbstractEntity;
use fv\Entity\EntitySchema;

abstract class AbstractQuery {

    /** @var string Entity Class Name */
    private $entityClassName;

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
     * @param string $entityClassName
     *
     * @return AbstractQuery
     */
    public function setEntityClassName( $entityClassName ) {
        $this->entityClassName = (string)$entityClassName;
        return $this;
    }

    /**
     * @return string Entity Class Name
     */
    public function getEntityClassName() {
        return $this->entityClassName;
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

    /**
     * @return \fv\Entity\EntitySchema
     */
    public function getSchema(){
        return EntitySchema::getSchema( $this->getEntityClassName() );
    }

}
