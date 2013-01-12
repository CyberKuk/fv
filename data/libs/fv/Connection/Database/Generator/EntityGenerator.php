<?php

namespace fv\Connection\Database\Generator;

use fv\Entity\AbstractEntity;
use fv\Connection\AbstractConnection;

abstract class EntityGenerator {

    /** @var AbstractConnection */
    private $connection;

    /** @var AbstractEntity[] */
    private $entities = [];

    /**
     * @param $connection
     * @return EntityGenerator
     */
    final protected function setConnection( AbstractConnection $connection ){
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return \fv\Connection\AbstractConnection
     */
    final public function getConnection(){
        return $this->connection;
    }

    /**
     * @param \fv\Entity\AbstractEntity $entity
     * @return EntityGenerator
     */
    final public function addEntity( AbstractEntity $entity ){
        $this->entities[] = $entity;
        return $this;
    }

    /**
     * @return \fv\Entity\AbstractEntity[]
     */
    final public function getEntities(){
        return $this->entities;
    }

    abstract public function generate();

}
