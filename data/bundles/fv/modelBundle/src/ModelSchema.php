<?php

namespace Bundle\fv\ModelBundle;

use Bundle\fv\ModelBundle\Reflection\ReflectionClass;
use \fv\Collection\Collection;

class ModelSchema {

    private $class;

    /** @var \fv\Collection\Collection */
    private $schema;

    /**
     * @param string $class class name
     */
    function __construct( $class ) {
        $this->setClass( $class );

        $reflection = new ReflectionClass( $class );

        $this->schema = $reflection->getSchema();
        $this->schema->fields = array();

        foreach( $reflection->getProperties() as $property ){
            $schema = $property->getSchema();

            if( $schema->field ){
                $property->setAccessible(true);
                $this->schema->fields->{$property->getName()} = new FieldSchema( $property, Field\AbstractField::build( $schema ) );
            }
        }
    }

    /**
     * @param $name
     * @return \fv\Collection\Collection
     */
    function __get( $name ) {
        return $this->schema->$name;
    }


    /**
     * @param $class
     *
     * @return ModelSchema
     */
    static function getSchema( $class ){
        static $instances;

        if( ! isset( $instances[$class] ) )
            $instances[$class] = new ModelSchema( $class );

        return $instances[$class];
    }

    public function mixInto( AbstractModel $class ){
        $fields = array();
        foreach( $this->schema->fields->leafs() as $key => $fieldSchema ){
            /** @var $fieldSchema FieldSchema */
            $fields[$key] = $fieldSchema->mixInto( $class );
        }
        return $fields;
    }

    public function setClass( $class ) {
        $this->class = $class;
        return $this;
    }

    public function getClass() {
        return $this->class;
    }

    public function getFields( $type = null ){
        $fields = $this->schema->fields->map( function( FieldSchema $fieldSchema ){
            return $fieldSchema->getPrototype();
        });

        if( ! is_null($type) ){
            $fields = $fields->filter( function( $field ) use ( $type ){
                return $field instanceof $type;
            } );
        }

        return $fields->keys();
    }

}
