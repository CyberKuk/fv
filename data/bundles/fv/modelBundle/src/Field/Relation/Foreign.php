<?php

namespace Bundle\fv\ModelBundle\Field\Relation;

use Bundle\fv\ModelBundle\AbstractModel;
use Bundle\fv\ModelBundle\Field\AbstractField;
use Bundle\fv\ModelBundle\ModelSchema;
use Bundle\fv\ModelBundle\Exception\ModelException;

class Foreign extends AbstractField {

    /** @var AbstractModel */
    private $model;
    private $modelName;
    private $keyName;

    public function setModelName( $class ){
        if( !class_exists($class) )
            throw new ModelException("Relation class {$class} not found");

        if( !is_subclass_of($class, "Bundle\\fv\\ModelBundle\\AbstractModel") )
            throw new ModelException("Relation class {$class} must be subclass of Bundle\\fv\\ModelBundle\\AbstractModel");

        $this->modelName = $class;

        $primaryKeys = $this->getSchema()->getPrimaryKeys();
        if( count($primaryKeys) > 1 ){
            throw new ModelException("Surrogate keys for related class {$class} are not supported");
        }

        $this->keyName = reset($primaryKeys);
    }

    public function getModelName(){
        return $this->modelName;
    }

    public function setModel( AbstractModel $model ) {
        $this->model = $model;
        return $this;
    }

    /**
     * @return AbstractModel
     */
    public function model() {
        if( ! $this->model && $this->value ){
            $this->model = $this->getModelByKey( $this->value );
        }

        return $this->model;
    }

    public function set( $value ){
        if( $value instanceof $this->modelName ){
            return $this->setModel( $value );
        }

        $this->model = null;
        return parent::set( $value );
    }

    public function get(){
        if( $this->model() ){
            return $this->model()->getField($this->keyName)->get();
        }

        return parent::get();
    }

    private function getModelByKey( $key ){
        return call_user_func( array( $this->getModelName(), "fetch" ), $key );
    }

    private function getSchema(){
        return ModelSchema::getSchema($this->getModelName());
    }

}
