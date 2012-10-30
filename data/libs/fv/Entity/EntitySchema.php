<?php

namespace fv\Entity;

use \fv\Reflection\ReflectionClass;

class EntitySchema {

    private $class;

    /** @var \fv\Collection */
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
     * @return \fv\Collection
     */
    function __get( $name ) {
        return $this->schema->$name;
    }


    /**
     * @param $class
     *
     * @return EntitySchema
     */
    static function getSchema( $class ){
        static $instances;

        if( ! isset( $instances[$class] ) )
            $instances[$class] = new EntitySchema( $class );

        return $instances[$class];
    }

    public function mixInto( AbstractEntity $class ){
        $fields = array();
        foreach( $this->schema->fields->getValues() as $key => $fieldSchema ){
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
        $fields = array_map( function( FieldSchema $fieldSchema ){
            return $fieldSchema->getPrototype();
        }, $this->fields->getValues() );

        if( ! is_null($type) ){
            $fields = array_filter( $fields, function( $field ) use ( $type ){
                return $field instanceof $type;
            } );
        }

        return array_keys($fields);
    }

}
