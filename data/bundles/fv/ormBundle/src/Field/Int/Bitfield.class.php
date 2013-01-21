<?php
/**
 * User: cah4a
 * Date: 06.03.12
 * Time: 17:45
 */

class Field_Int_Bitfield extends AbstractField_Int {

    function add( $value ){
        $this->set( $this->get() | $value );
    }

    function is( $value ){
        return ($this->get() & $value) > 0;
    }

    function rem( $value ){
        $this->set( $this->get() & ~$value );
    }

    function set( $value ){
        if( is_array($value) ){
            $supervalue = 0;
            foreach( $value as $v ){
                $supervalue |= (int)$v;
            }
            $value = $supervalue;
        }

        return parent::set( $value );
    }

    function asArray(){
        $dec = decbin( $this->get() );
        $result = array();
        for( $i = 0 ; $i < strlen($dec); $i++ ){
            if( $dec[$i] == "1" )
                $result[] = pow("2", strlen($dec) - $i - 1);
        }
        return $result;
    }

    function getList( fvRoot $entity ){
        $methodName = "get" . ucfirst($this->key) . "Multilist";
        return $entity->$methodName();
    }

    function getEditMethod(){
        return self::EDIT_METHOD_MULTILIST;
    }

}