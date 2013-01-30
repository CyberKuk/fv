<?php

namespace Bundle\fv\ModelBundle\Field\Relation;

use Bundle\fv\ModelBundle\Field\ArrayField;
use Bundle\fv\ModelBundle\Exception\ModelException;
use Bundle\fv\ModelBundle\ModelSchema;
use Bundle\fv\ModelBundle\AbstractModel;

class Constraint extends ArrayField {

    private $modelName;
    private $key;

    public function setModelName( $class ) {
        if( !class_exists($class) )
            throw new ModelException("Relation class {$class} not found");

        if( !is_subclass_of($class, "Bundle\\fv\\ModelBundle\\AbstractModel") )
            throw new ModelException("Relation class {$class} must be subclass of Bundle\\fv\\ModelBundle\\AbstractModel");

        $this->modelName = $class;
    }

    public function getModelName() {
        return $this->modelName;
    }

    public function setKey( $key ) {
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }

    private $ownerKey;
    private function getOwnerKey(){
        if( empty($this->ownerKey) ){
            $fields = $this->getOwner()->getPrimaryFields();

            if( count($fields) > 1 ){
                $class = get_class($this->getOwner());
                throw new ModelException( "Surrogate keys for relation class {$class} are not supported" );
            }

            $this->ownerKey = reset($fields)->get();
        }

        return $this->ownerKey;
    }

    public function get() {
        /** @var $query \Bundle\fv\ModelBundle\Query\Database\DatabaseQuery */
        $query = call_user_func( array($this->getModelName(), "query") );

        if( !method_exists($query, "where") )
            throw new ModelException( "Default query of class {$this->getModelName()} must implement 'where' method for relation loading" );

        if( !method_exists($query, "fetchAll") )
            throw new ModelException( "Default query of class {$this->getModelName()} must implement 'fetchAll' method for relation loading" );

        return $query->where( array( $this->getKey() => $this->getOwnerKey() ) )->fetchAll();
    }

    private function getSchema(){
        return ModelSchema::getSchema($this->getModelName());
    }

}
