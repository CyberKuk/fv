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
    private function __construct( $class ) {
        $this->setClass( $class )
            ->createFields()
            ->createIndexes();
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
        static $instances = array();

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

    private function setClass( $class ) {
        $this->class = $class;
        return $this;
    }

    public function getClass() {
        return $this->class;
    }

    /**
     * @param null $type
     * @return string[]
     */
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

    private function createFields() {
        $reflection = new ReflectionClass( $this->class );

        $this->schema = $reflection->getSchema();
        $this->schema->fields = array();

        foreach( $reflection->getProperties() as $property ){
            $schema = $property->getSchema();

            if( $schema->field ){
                $property->setAccessible(true);
                $this->schema->fields->{$property->getName()} = new FieldSchema( $property, Field\AbstractField::build( $schema ) );
            }
        }

        return $this;
    }

    private function createIndexes() {
        $indexes = array();

        if( $this->schema->indexes ){
            /** @var $config Collection */
            foreach( $this->schema->indexes as $type => $config ){
                $class = __NAMESPACE__ . "\\Index\\" . $type;

                foreach( $config as $index ){
                    foreach( $index as $definition ){
                        $indexFields = array_unique( $definition->leafs() );
                        foreach( $indexFields as $field ){
                            if( ! $this->schema->fields->$field ){
                                throw new \Bundle\fv\ModelBundle\Exception\ModelException("No field {$field} in {$this->getClass()} class to create index");
                            }
                        }
                        $indexes[] = new $class($indexFields);
                    }
                }
            }
        }

        $this->schema->indexes = $indexes;

        return $this;
    }

    /**
     * @param null|string $class
     * @return Index\PrimaryIndex[]
     */
    public function getIndexes( $class = null ){
        if( is_null($class) )
            return $this->indexes;

        if( ! $this->indexes )
            return array();

        if( ! is_object( $class ) ){
            $class = (string)$class;

            if( substr( $class, 0, 1 ) !== "\\" ){
                $class = __NAMESPACE__ . "\\Index\\" . $class;
            }
        }

        $result = $this->schema->indexes->filter( function( Collection $index ) use ( $class ){
            if( !$index->isLeaf() )
                return false;
            return $index->get() instanceof $class;
        } )->leafs();

        foreach( $result as &$key ){
            $key = $key->get();
        }
        return $result;
    }

    public function getPrimaryKeys(){
        $primaryIndex = $this->getIndexes("PrimaryIndex");

        if( isset( $primaryIndex[0] ) ){
            return $primaryIndex[0]->getFields();
        }

        return array();
    }
}
