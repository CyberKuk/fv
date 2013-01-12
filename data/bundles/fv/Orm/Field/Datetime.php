<?php

namespace Bundle\fv\Orm\Field;

class Datetime extends AbstractField {
    
    function getEditMethod() {
        return self::EDIT_METHOD_DATETIME;
    }

    function set( $value ){
        if( is_integer($value) )
            $value = date('Y-m-d H:i:s', $value);

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
        if( $this->get() == '0000-00-00 00:00:00' )
            return '';

        return '<nobr>' . date('d.m.y', $this->asTimestamp()) . ' <small>' . date('H:i', $this->asTimestamp()) . '</small></nobr>';
    }

    function asMysql(){
        if( !is_null($this->get()) )
            return date( 'Y-m-d H:i:s', strtotime($this->get()) );

        return null;
    }

    function getSQlPart() {
        $isNull = $this->isNullable() ? 'NULL' : 'NOT NULL';

        if (is_null($this->get())) {
            $default = $this->isNullable() ? 'DEFAULT NULL' : '';
        } else {
            $default = "DEFAULT '".$this->get()."'";
        }
        return  "datetime $isNull $default";
    }
    
}