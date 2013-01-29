<?php

namespace Bundle\fv\ModelBundle\Index;

abstract class AbstractIndex {

    private $fields = array();

    final public function __construct( array $fieldNames ) {
        $this->fields = array_values( $fieldNames );
    }

    final public function getFields() {
        return $this->fields;
    }
}
