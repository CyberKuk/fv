<?php

namespace Bundle\fv\Orm\Field;

use \Bundle\fv\Orm\ManagersPool;
use \Bundle\fv\Orm\Root;

class Field_Foreign extends Int {

    protected $entityName;
    protected $where;
    protected $key;

    static $cache = array();

    function __construct( array $fieldSchema, $key ) {
        parent::__construct( $fieldSchema, $key );

        $this->entityName = $fieldSchema[ 'entity' ];
        $this->where = $fieldSchema[ 'where' ];
    }

    function getEditMethod() {
        return self::EDIT_METHOD_LIST;
    }

    public function asAdorned() {
        $manager = ManagersPool::get( $this->entityName );
        $entity = $manager->getByPk( $this->get() );

        if( method_exists( $entity, "asAdorned" ) ){
            return $entity->asAdorned();
        }

        return $entity;
    }

    function getList( Root $entity ) {
        $foreigns = ManagersPool::get( $this->entityName )->getAll( $this->where );

        $result = array( );

        if ( $this->nullable ) {
            $result[ '0' ] = fvDictionary::getInstance()->get('No', 'Нет');
        }

        foreach ( $foreigns as $entity ) {
            $result[ $entity->getPk() ] = ( string ) $entity;
        }

        return $result;
    }

    function asMysql() {
        if ( !$this->get() )
            return null;

        return $this->get();
    }

    function asEntity() {
        if ( !isset(self::$cache[$this->entityName][$this->get()]) ){
            self::$cache[$this->entityName][$this->get()] = ManagersPool::get( $this->entityName )->getByPk( $this->get(), true );
        }

        return self::$cache[$this->entityName][$this->get()];
    }

    function getForeignEntityName(){
        return $this->entityName;
    }

    static function preloadCache( $entityName, $entities = array() ){
        if( isset(self::$cache[$entityName]) )
            self::$cache[$entityName] = array_replace($entities, self::$cache[$entityName]);
        else
            self::$cache[$entityName] = $entities;
    }

    public function getEntityName() {
        return $this->entityName;
    }

    static function clearCache(){
        self::$cache = array();
    }

    function getForeignEntityTableName() {
        $entity = new $this->entityName;
        return $entity->getTableName();
    }

    function getForeignEntityPkName() {
        $entity = new $this->entityName;
        return $entity->getPkName();
    }

    function set( $value ){
        if( is_object( $value ) ){
            if( !$value instanceof $this->entityName ){
                $givenClass = get_class( $value );
                throw new VerboseException( "Instance of '{$this->entityName}' expected '{$givenClass}' given." );
            }

            if( $value->isNew() ){
                throw new VerboseException( "Cannot verbose non saved object!" );
            }

            parent::set( $value->getPk() );
        }
        else{
            parent::set( $value );
        }

    }
}