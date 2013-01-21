<?php

/**
* Счетчик просмотров на редиске)
* @see Redis
* @see Rediska
*/
class Field_Int_Views extends AbstractField_Int implements iBehaviour {
    private $namespace;  
    private $pk;

    function __construct( array $fieldSchema, $name ){
        if ( isset( $fieldSchema[ 'languaged' ] ) )
            throw new EFieldError( "Field '{$name}' cannot be languagable" );

        if ( isset( $fieldSchema[ 'namespace' ] ) )
            $this->namespace = $fieldSchema[ 'namespace' ];
            
        parent::__construct( $fieldSchema, $name ); 
    }
    
    function update(){
        $redisInstance = new Rediska( array( "namespace" => $this->namespace ) );
        $key = new Rediska_Key( $this->pk );
        $this->value = $key->getValue();
    }

    function setPk( $key ){

        if( !is_array( $key ) )  
            $this->pk = $key;
        else
            $this->pk = implode("-", $key); 

        $this->update();
    }

    // set is not allowed
    function set( $value ){
        return $this->get();
    }   

    function get(){  
        if( !$this->pk )
            return 0;

        $this->update();    
        return intval( $this->value );
    }

    function __toString(){
        return (string)$this->get();
    }
}