<?php

namespace fv\Entity\Field;

use \fv\Collection;

abstract class AbstractField {

    private $value;
    private $isChanged = false;

    public static function build( $schema ){
        if( is_array($schema) )
            $schema = new Collection( $schema );

        if( $schema->class ){
            $class = $schema->class;
        }
        elseif( $schema->var ){
            $class = $schema->var->type;
        } else
            throw new \Exception("Can't create undefined class Field");

        $field = new $class;

        foreach( $schema->getValues() as $key => $value ){
            if( in_array( $key, array("field", "class", "var")) )
                continue;

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

}
