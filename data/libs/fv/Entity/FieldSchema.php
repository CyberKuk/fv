<?php

namespace fv\Entity;

use \fv\Reflection\ReflectionProperty;
use \fv\Entity\Field\AbstractField;

class FieldSchema {

    /** @var ReflectionProperty */
    private $property;

    /** @var AbstractField */
    private $prototype;

    public function __construct( ReflectionProperty $property, AbstractField $prototype ) {
        $this->setProperty($property);
        $this->setPrototype($prototype);
    }

    public function mixInto( AbstractEntity $class ){
        $field = clone $this->getPrototype();
        $this->getProperty()->setValue( $class, $field );
        return $field;
    }

    /**
     * @param \fv\Reflection\ReflectionProperty $property
     * @return \fv\Entity\FieldSchema
     */
    public function setProperty( $property ) {
        $this->property = $property;
        return $this;
    }

    /**
     * @return \fv\Reflection\ReflectionProperty
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param \fv\Entity\Field\AbstractField $prototype
     * @return \fv\Entity\FieldSchema
     */
    public function setPrototype( $prototype ) {
        $this->prototype = $prototype;
        return $this;
    }

    /**
     * @return \fv\Entity\Field\AbstractField
     */
    public function getPrototype() {
        return $this->prototype;
    }


}
