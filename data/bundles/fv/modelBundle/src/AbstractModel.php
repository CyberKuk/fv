<?php

namespace Bundle\fv\ModelBundle;

use Bundle\fv\ModelBundle\Field\AbstractField;
use Bundle\fv\ModelBundle\Query\QueryBuilder;
use fv\Connection\ConnectionFactory;

use Bundle\fv\ModelBundle\Exception\ModelException;
use Bundle\fv\ModelBundle\Exception\FieldNotFoundException;

/**
 * Abstract Model is base class to use to describe the domain model.
 */
abstract class AbstractModel {

    /** @var \Bundle\fv\ModelBundle\Field\AbstractField[] */
    private $fields;

    /** @var \Bundle\fv\ModelBundle\Index\AbstractIndex[] */
    private $indexes;

    /**
     * Mix Model schema to new Instance
     */
    final public function __construct(){
        $this->fields = self::getSchema()->mixInto( $this );
    }

    /**
     * Use build method to get Schema for static class
     *
     * @return ModelSchema
     */
    private static function getSchema(){
        return ModelSchema::getSchema( get_called_class() );
    }

    /**
     * Proxy method to create update query
     *
     * @param array $update
     * @return Query\AbstractQuery|Query\Database\DatabaseQuery
     */
    public static function update( array $update = array() ){
        return self::query()->update( $update );
    }

    /**
     * Proxy method to create select query
     *
     * @param null $selectString
     * @return Query\Database\DatabaseQuery|Query\AbstractQuery
     */
    public static function select( $selectString = null ){
        if( is_null($selectString) ){
            return self::query();
        }

        return self::query()->select( $selectString );
    }

    /**
     * Proxy method to create query
     *
     * @param null $connectionName
     * @return Query\Database\DatabaseQuery|Query\AbstractQuery
     */
    public static function query( $connectionName = null ){
        if( is_null( $connectionName ) ){
            $connectionName = self::getDefaultConnectionName();
        }

        return QueryBuilder::getQueryToConnection($connectionName)->setModelClassName(get_called_class());
    }

    /**
     * @return \fv\Connection\AbstractConnection
     */
    static public function getDefaultConnection(){
        $connectionFactory = new ConnectionFactory;
        return $connectionFactory->getConnection( self::getDefaultConnectionName() );
    }

    /**
     * @return string
     */
    static public function getDefaultConnectionName(){
        if( ! self::getSchema()->connection ){
            throw new ModelException("Default connection not set");
        }

        return self::getSchema()->connection->get();
    }

    public function persist( $connectionName = null ){
        return self::query( $connectionName )->persist( $this );
    }

    public function remove( $connectionName = null ){
        self::query( $connectionName )->remove( $this );
    }

    /**
     * @param      $key
     * @param null $connectionName
     *
     * @return $this
     */
    public static function fetch( $key, $connectionName = null ){
        return self::query( $connectionName )->fetch( $key );
    }

    /**
     * @param null|string $class
     * @return Field\AbstractField[]
     */
    public function getFields( $class = null ){
        if( is_null($class) )
            return $this->fields;

        if( empty($this->fields) )
            return array();

        if( ! is_object( $class ) ){
            $class = (string)$class;

            if( substr( $class, 0, 1 ) !== "\\" ){
                $class = __NAMESPACE__ . "\\Field\\" . $class;
            }
        }

        return array_filter( $this->fields, function( $field ) use ( $class ){
            return $field instanceof $class;
        } );
    }

    /**
     * @param $name
     *
     * @throws Exception\FieldNotFoundException
     * @return Field\AbstractField
     */
    public function getField( $name ){
        if( isset( $this->fields[$name] ) )
            return $this->fields[$name];

        throw new FieldNotFoundException( "Trying to get field {$name} witch does not exist in class " . get_class($this) );
    }

    /**
     * @return Field\AbstractField[]
     */
    public function getPrimaryFields() {
        return array_intersect_key( $this->getFields(), array_flip( self::getSchema()->getPrimaryKeys() ) );
    }
}
