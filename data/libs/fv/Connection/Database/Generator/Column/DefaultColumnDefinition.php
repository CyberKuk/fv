<?php

namespace fv\Connection\Database\Generator\Column;

use fv\Entity\Field\AbstractField;

abstract class DefaultColumnDefinition {

    /** @var AbstractField */
    private $field;

    final private function __construct( AbstractField $field ) {
        $this->setField( $field );
    }

    public static function build( AbstractField $field ){
        if( $field instanceof \fv\Entity\Field\Int )
            return new IntColumnDefinition( $field );

        throw new \fv\Connection\Database\Generator\Exception\ColumnDefinitionException("Column definition for class '".get_class($field)."' not found!");
    }

    /**
     * @param \fv\Entity\Field\AbstractField $field
     * @return DefaultColumnDefinition
     */
    public function setField( AbstractField $field ) {
        $this->field = $field;
        return $this;
    }

    /**
     * @return \fv\Entity\Field\AbstractField
     */
    public function getField() {
        return $this->field;
    }

    abstract public function __toString();
}
