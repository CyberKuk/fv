<?php

namespace Bundle\fv\ModelBundle;

use Bundle\fv\ModelBundle\Reflection\ReflectionProperty;
use Bundle\fv\ModelBundle\Field\AbstractField;

class FieldSchema {

    /** @var \Bundle\fv\ModelBundle\Reflection\ReflectionProperty */
    private $property;

    /** @var AbstractField */
    private $prototype;

    public function __construct( ReflectionProperty $property, AbstractField $prototype ) {
        $this->setProperty($property);
        $this->setPrototype($prototype);
    }

    public function mixInto( AbstractModel $class ){
        $field = clone $this->getPrototype();
        $this->getProperty()->setValue( $class, $field );
        return $field;
    }

    /**
     * @param \Bundle\fv\ModelBundle\Reflection\ReflectionProperty $property
     * @return \Bundle\fv\ModelBundle\FieldSchema
     */
    public function setProperty( $property ) {
        $this->property = $property;
        return $this;
    }

    /**
     * @return \Bundle\fv\ModelBundle\Reflection\ReflectionProperty
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param \Bundle\fv\ModelBundle\Field\AbstractField $prototype
     * @return \Bundle\fv\ModelBundle\FieldSchema
     */
    public function setPrototype( $prototype ) {
        $this->prototype = $prototype;
        return $this;
    }

    /**
     * @return \Bundle\fv\ModelBundle\Field\AbstractField
     */
    public function getPrototype() {
        return $this->prototype;
    }


}
