<?php

namespace OrmBundle\Field;

class Float extends AbstractField {

    /**
     * Знаковое ли поле
     * @var bool $unsigned
     */
    protected $unsigned = true;

    public function isValid(){
        if( $this->nullable && is_null( $this->value ) )
            return true;

        return is_float( $this->value );
    }
    
    function getEditMethod() {
        return self::EDIT_METHOD_INPUT;
    }
    
    function set( $value ){
        if( is_null($value) )
            parent::set( null );
        else
            parent::set( (float)$value );
    }

    function updateSchema( array $fieldSchema ) {
        if ( isset( $fieldSchema[ 'unsigned' ] ) )
            $this->unsigned = $fieldSchema[ 'unsigned' ];

        parent::updateSchema($fieldSchema);

    }

    function getSQlPart() {
        $isNull = $this->nullable ? 'NULL' : 'NOT NULL';

        $unsigned = $this->unsigned ? 'unsigned' : '';

        if (is_null($this->get())) {
            $default = $this->nullable ? 'DEFAULT NULL' : '';
        } else {
            $default = "DEFAULT '".$this->get()."'";
        }
        return  "FLOAT  $unsigned $isNull $default";
    }
    
}