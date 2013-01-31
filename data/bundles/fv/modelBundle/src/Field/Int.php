<?php
/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 11:28
 */

namespace Bundle\fv\ModelBundle\Field;

class Int extends AbstractField {

    private $unsigned;
    private $autoincrement;
    private $type;
    private $length;

    public function set($value) {
        if( is_null($value) && $this->isNullable() )
            return parent::set(null);

        return parent::set((int)$value);
    }

    public function setDefault($default) {
        if( is_null($default) )
            return parent::setDefault(null);

        return parent::setDefault((int)$default);
    }


    public function setLength( $maxLength ) {
        $this->length = $maxLength;
        return $this;
    }

    public function getLength() {
        return $this->length;
    }

    public function setType( $type ) {
        $this->type = $type;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setUnsigned( $unsigned ) {
        $this->unsigned = $unsigned;
        return $this;
    }

    public function getUnsigned() {
        return $this->unsigned;
    }

    public function setAutoincrement($autoincrement) {
        $this->autoincrement = $autoincrement;
    }

    public function isAutoincrement() {
        return $this->autoincrement;
    }

}
