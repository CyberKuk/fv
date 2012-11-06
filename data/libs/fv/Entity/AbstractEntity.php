<?php

namespace fv\Entity;

use fv\Entity\Query\AbstractQuery;
use fv\Entity\Field\AbstractField;
use fv\Connection\ConnectionFactory;

use fv\Entity\Exception\EntityException;
use fv\Entity\Exception\FieldNotFoundException;

/**
 * User: cah4a
 * Date: 05.10.12
 * Time: 18:19
 */
class AbstractEntity {

    /** @var \fv\Entity\AbstractField[] */
    private $fields;

    final public function __construct(){
        $this->fields = self::getSchema()->mixInto( $this );
    }

    final public static function getSchema(){
        return EntitySchema::getSchema( get_called_class() );
    }

    /**
     * @param array $update
     *
     * @return Query\Database\DatabaseQuery
     */
    public static function update( array $update = array() ){
        return self::query()->update( $update );
    }

    /**
     * @param null $selectString
     * @return Query\Database\DatabaseQuery
     */
    public static function select( $selectString = null ){
        return self::query()->select( $selectString );
    }

    /**
     * @param null $connectionName
     *
     * @return Query\AbstractQuery
     */
    public static function query( $connectionName = null ){
        if( is_null( $connectionName ) ){
            $connectionName = self::getDefaultConnectionName();
        }

        $connectionFactory = new ConnectionFactory;
        return $connectionFactory->getConnection( $connectionName )->createQuery()
            ->setEntity( get_called_class() );
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
        return self::getSchema()->connection;
    }

    public function persist( $connectionName = null ){
        return self::query( $connectionName )->persist( $this );
    }

    public function remove( $connectionName = null ){
        return self::query( $connectionName )->remove( $this );
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
     * @return Field\Primary[]
     */
    public function getPrimaryFields(){
        return $this->getFields('Primary');
    }

}
