<?php

namespace Bundle\fv\Orm\Field;

class Date extends AbstractField {
    
    function getEditMethod() {
        return self::EDIT_METHOD_DATE;
    }
    
    function asMysql(){
        if( !is_null($this->get()) )
            return date( 'Y-m-d', strtotime($this->get()) );

        return null;
    }

    function set( $value ){
        if( is_integer($value) )
            $value = date('Y-m-d', $value);

        if( is_string($value) ){
            $value = trim($value);

            if( empty($value) )
                $value = null;
        }
        
        parent::set($value);
    }

    function asTimestamp(){
        return strtotime( $this->get() );
    }

    function asAdorned(){
        if( !$this->asTimestamp() )
            return '';

        return '<nobr>' . date('d.m.y', $this->asTimestamp()) . '</nobr>';
    }

    function getSQlPart() {
        $isNull = $this->nullable ? 'NULL' : 'NOT NULL';

        if (is_null($this->get())) {
            $default = $this->nullable ? 'DEFAULT NULL' : '';
        } else {
            $default = "DEFAULT '".$this->get()."'";
        }
        return  "date $isNull $default";
    }
}