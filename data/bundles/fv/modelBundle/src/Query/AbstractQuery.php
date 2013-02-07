<?php

namespace Bundle\fv\ModelBundle\Query;

use fv\Connection\AbstractConnection;
use Bundle\fv\ModelBundle\AbstractModel;
use Bundle\fv\ModelBundle\ModelSchema;

abstract class AbstractQuery {

    /** @var string Model Class Name */
    private $modelClassName;

    /** @var AbstractConnection */
    private $connection;

    abstract public function fetch( $key );
    abstract public function persist( AbstractModel $model );
    abstract public function remove( AbstractModel $model );

    /**
     * @param \fv\Connection\AbstractConnection $connection
     */
    final public function __construct( AbstractConnection $connection ){
        $this->setConnection( $connection );
    }

    /**
     * @param string $modelClassName
     *
     * @return AbstractQuery
     */
    public function setModelClassName( $modelClassName ) {
        $this->modelClassName = (string)$modelClassName;
        return $this;
    }

    /**
     * @return string Model Class Name
     */
    public function getModelClassName() {
        return $this->modelClassName;
    }

    /**
     * @param \fv\Connection\AbstractConnection $connection
     * @return \Bundle\fv\ModelBundle\Query\AbstractQuery
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
     * @return \Bundle\fv\ModelBundle\ModelSchema
     */
    public function getSchema(){
        return ModelSchema::getSchema( $this->getModelClassName() );
    }


    /**
     * @param array $map
     * @return AbstractModel
     */
    protected function createModel( array $map ){
        return \Bundle\fv\ModelBundle\Query\ModelsPool::create($this->getModelClassName(), $map);
    }
}
