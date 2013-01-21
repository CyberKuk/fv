<?php

namespace OrmBundle\Field;

class String extends AbstractField {

    const DEF_LENGTH=255;

    function getEditMethod() {
        return self::EDIT_METHOD_INPUT;
    }
    
    function set( $value ) {
        if( is_string($value) ) {
            if( strlen( $value ) == 0 ) {
                parent::set( null );
                return;
            }
        }
        
        parent::set( $value );
    }

    function getSQlPart() {
        if (!$this->length) $this->length = self::DEF_LENGTH;
        $isNull = $this->nullable ? 'NULL' : 'NOT NULL';

        if (is_null($this->get())) {
            $default = $this->nullable ? 'DEFAULT NULL' : '';
        } else {
            $default = "DEFAULT '".$this->get()."'";
        }
        return  "varchar({$this->length}) $isNull $default";
    }
}