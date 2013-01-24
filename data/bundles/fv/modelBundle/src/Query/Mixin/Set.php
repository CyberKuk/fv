<?php

namespace Bundle\fv\ModelBundle\Query\Mixin;

trait Set {

    private $set = array();

    final public function set( $values ){
        return $this->clearSet()->addSet( $values );
    }

    final public function addSet( $values ){
        foreach( $values as $key => $value ){
            $this->andSet($key, $value);
        }

        return $this;
    }

    final public function andSet( $key, $value ){
        $this->set[$key] = $value;
        return $this;
    }

    final public function clearSet(){
        $this->set = array();
        return $this;
    }

    final protected function getSetKeys(){
        return array_keys($this->set);
    }

    final protected function getSetParams(){
        return $this->set;
    }

}
