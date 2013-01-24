<?php

namespace Bundle\fv\ModelBundle\Field\Datetime;

class Date extends DateTime {

    private $format = "Y-m-d";

    public function setFormat( $format ) {
        $this->format = $format;
        return $this;
    }

    public function getFormat() {
        return $this->format;
    }

}
