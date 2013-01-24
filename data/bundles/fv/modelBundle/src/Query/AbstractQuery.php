<?php

namespace Bundle\fv\ModelBundle\Query;

use fv\Connection\AbstractConnection;
use Bundle\fv\ModelBundle\AbstractModel;
use Bundle\fv\ModelBundle\ModelSchema;

abstract class AbstractQuery {

    /** @var string Model Class Name */
    private $ModelClassName;

    /** @var AbstractConnection */
    private $connection;

    abstract public function fetch( $key );
    abstract public function persist( AbstractModel $Model );
    abstract public function remove( AbstractModel $Model );

    /**
     * @param \fv\Connection\AbstractConnection $connection
     */
    final public function __construct( AbstractConnection $connection ){
        $this->setConnection( $connection );
    }

    /**
     * @param string $ModelClassName
     *
     * @return AbstractQuery
     */
    public function setModelClassName( $ModelClassName ) {
        $this->ModelClassName = (string)$ModelClassName;
        return $this;
    }

    /**
     * @return string Model Class Name
     */
    public function getModelClassName() {
        return $this->ModelClassName;
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

}
