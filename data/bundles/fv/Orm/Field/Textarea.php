<?php

namespace Bundle\fv\Orm\Field;

class Textarea extends String {
    
    function getEditMethod() {
        return self::EDIT_METHOD_TEXTAREA;
    }

    function asArray(){
        $results = explode( "\r", preg_replace( "/[\s\t]*(\r|\n|\r\n|\n\r)[\s\t]*/mu", "\r", $this->get() ) );
        foreach( $results as $key => $value ){
            if( !$value )
                unset( $results[$key] );
        }
        return $results;
    }
    function getSQlPart() {
        return  "text";
    }
}