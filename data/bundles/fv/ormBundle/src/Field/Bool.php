<?php

namespace OrmBundle\Field;

class Bool extends AbstractField {

    function getEditMethod() {
        return self::EDIT_METHOD_CHECKBOX;
    }

    function set( $value ){
        if( is_null($value) )
            parent::set( null );
        elseif ($value)
            parent::set( 1 );
        else
            parent::set( 0 );
    }

    public function __toString(){
        if( $this->get() )
            return (string)fvDictionary::getInstance()->BOOLEAN_TRUE;
        else
            return (string)fvDictionary::getInstance()->BOOLEAN_FALSE;
    }

    public function asAdorned(){
        if( $this->get() )
            return 'Да';
        else
            return 'Нет';
    }

    function getSQlPart() {
        $default = "DEFAULT '".(int)$this->get()."'";
        return  "int(1) unsigned NOT NULL $default";
    }
}