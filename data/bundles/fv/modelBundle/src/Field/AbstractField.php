<?php

namespace Bundle\fv\ModelBundle\Field;

use \fv\Collection\Collection;
use Bundle\fv\ModelBundle\AbstractModel;

abstract class AbstractField {

    private $nullable = true;
    private $default = null;
    private $isChanged = false;

    /** @var AbstractModel */
    private $owner;

    protected $value;

    public static function build( $schema ){
        if( is_array($schema) )
            $schema = new Collection( $schema );

        if( $schema->class ){
            $class = $schema->class->get();
        }
        elseif( $schema->var ){
            $class = $schema->var->type->get();
        } else
            throw new \Exception("Can't create undefined class Field");

        $field = new $class;

        foreach( $schema->field->leafs() as $key => $value ){
            $method = "set" . ucfirst($key);
            $field->$method($value);
        }

        return $field;
    }

    public function get(){
        return $this->value;
    }

    public function set( $value ){
        if( $this->value != $value ){
            $this->value = $value;
            $this->changed();
        }
        return $this;
    }

    public function asMysql(){
        return $this->get();
    }

    public function fromMysql( $value ){
        return $this->set( $value );
    }

    public function setIsChanged( $isChanged ) {
        $this->isChanged = (bool)$isChanged;
        return $this;
    }

    public function changed() {
        $this->isChanged = true;
        return $this;
    }

    public function notChanged() {
        $this->isChanged = false;
        return $this;
    }

    public function isChanged() {
        return $this->isChanged;
    }


    public function setNullable( $nullable ) {
        $this->nullable = $nullable;
        return $this;
    }

    public function isNullable() {
        return $this->nullable;
    }

    public function setDefault( $default ) {
        if( ! $this->isChanged() ){
            $this->default = $default;
            $this->set( $default );
            $this->notChanged();
        }
        return $this;
    }

    public function getDefault() {
        return $this->default;
    }

    /**
     * @return \Bundle\fv\ModelBundle\AbstractModel
     */
    protected function getOwner() {
        return $this->owner;
    }

    private function setOwner( AbstractModel $class ) {
        $this->owner = $class;
        return $this;
    }

    public function cloneFor( AbstractModel $class ) {
        $field = clone $this;
        return $field->setOwner($class);
    }
}
