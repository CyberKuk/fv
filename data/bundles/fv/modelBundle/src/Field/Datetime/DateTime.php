<?php

namespace Bundle\fv\ModelBundle\Field\Datetime;

use Bundle\fv\ModelBundle\Field\AbstractField;

class DateTime extends AbstractField {

    private $format = "Y-m-d H:i:s";

    public function setFormat( $format ) {
        $this->format = $format;
        return $this;
    }

    public function getFormat() {
        return $this->format;
    }

    public function set( $value ) {
        if( is_string($value) )
            $value = trim($value);

        if( empty($value) )
            return parent::set( null );

        if( !is_numeric( $value ) ) {
            $value = strtotime($value);
        }

        return parent::set( date($this->getFormat(), $value) );
    }

}
