<?php

namespace fv\Reflection;

class Schema {

    private $params = array();

    function __construct( array $value = null ){
        if( !is_null( $value ) ){
            foreach( $value as $key => $var )
                $this->$key = $var;
        }
    }

    /**
     * @param $name
     *
     * @return Schema|mixed
     */
    function __get( $name ) {
        if( !isset($this->params[$name]) )
            return null;

        return $this->params[$name];
    }

    function __set( $name, $value ) {
        if( is_array($value) )
            $this->params[$name] = new Schema( $value );
        else
            $this->params[$name] = $value;
    }

    function getValues(){
        return array_filter( $this->params, function( $param ){
            return ! $param instanceof Schema;
        });
    }

}
