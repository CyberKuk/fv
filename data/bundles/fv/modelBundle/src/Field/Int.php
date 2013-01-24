<?php
/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 11:28
 */

namespace Bundle\fv\ModelBundle\Field;

class Int extends AbstractField {

    private $nullable;

    private $default;

    private $unsigned;

    private $type;

    private $maxLength;

    public function setDefault( $default ) {
        if( ! $this->isChanged() ){
            $this->default = $default;
            $this->set( $default );
            $this->notChanged();
        }
        return $this;
    }

    public function getDefault() {
        return $this->default;
    }

    public function setMaxLength( $maxLength ) {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function getMaxLength() {
        return $this->maxLength;
    }

    public function setNullable( $nullable ) {
        $this->nullable = $nullable;
        return $this;
    }

    public function getNullable() {
        return $this->nullable;
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

}
