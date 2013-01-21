<?php

namespace OrmBundle\Field;

class FieldArray extends Textarea implements \ArrayAccess, \Iterator {

    function set( $value ){
        if( is_null($value) )
            $value = Array();

        if( !is_array( $value ) )
            $value = (array)@unserialize($value);

        parent::set( $value );
    }

    function setDefaultValue(){
        $this->value = Array();
    }

    public function asString(){
        return print_r($this->get(), true);
    }

    public function asArray(){
        return (array)$this->value;
    }

    public function asMysql(){
        return serialize( (array)$this->value );
    }



    // Array Access
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->setChanged( true );
            $this->value[] = $value;
        } else {
            $this->setChanged( !isset($this->value[$offset]) || $this->value[$offset] != $value );
            $this->value[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->value[$offset]);
    }

    public function offsetUnset($offset) {
        if( isset($this->value[$offset]) )
            $this->setChanged( true );

        unset($this->value[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->value[$offset]) ? $this->value[$offset] : null;
    }
    // End


    protected $cur = 0;

    //Foreach Access
    public function rewind() {
        $this->cur = 0;
    }

    public function key() {
        return $this->value[$this->cur];
    }

    public function current() {
        $function = 'get' . $this->value[$this->cur];
        return $this->$function();
    }

    public function next() {
        ++$this->cur;
    }

    public function valid() {
        return $this->cur < count($this->value);
    }
    // End


}