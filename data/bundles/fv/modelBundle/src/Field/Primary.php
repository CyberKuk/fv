<?php

namespace Bundle\fv\ModelBundle\Field;

use Bundle\fv\ModelBundle\Exception\FieldSetException;

class Primary extends AbstractField {

    private $autoIncrement = true;

    function setAutoIncrement( $bool ){
        $this->autoIncrement = $bool;
        return $this;
    }

    function isAutoIncrement(){
        return $this->autoIncrement;
    }

    function set( $value ){
        if( $this->get() )
            throw new FieldSetException("Primary key already defined!");

        return parent::set($value);
    }

}