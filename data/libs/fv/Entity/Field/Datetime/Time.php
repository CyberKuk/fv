<?php

namespace fv\Entity\Field\Datetime;

/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 13:10
 */
class Time extends DateTime {

    private $format = "H:i:s";

    public function setFormat( $format ) {
        $this->format = $format;
        return $this;
    }

    public function getFormat() {
        return $this->format;
    }

}
