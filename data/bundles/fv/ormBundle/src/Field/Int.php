<?php

namespace OrmBundle\Field;

class Int extends AbstractField {

    /**
     * Знаковое ли поле
     * @var bool $unsigned
     */
    protected $unsigned = true;

    const DEF_LENGTH=11;

    public function isValid(){
        if( $this->nullable && is_null( $this->value ) ){
            return true;
        }
        
        return is_numeric( $this->value );
    }
    
    function getEditMethod() {
        return self::EDIT_METHOD_INPUT;
    }
    
    function set( $value ){
        if( is_null($value) )
            parent::set( null );
        else
            parent::set( (int)$value );
    }

    function updateSchema( $fieldSchema ) {
        if ( isset( $fieldSchema[ 'unsigned' ] ) )
            $this->unsigned = $fieldSchema[ 'unsigned' ];

        parent::updateSchema($fieldSchema);
    }

    function getSQlPart() {
        if (!$this->length) $this->length=self::DEF_LENGTH;
        $isNull = $this->nullable ? 'NULL' : 'NOT NULL';

        $unsigned = $this->unsigned ? 'unsigned' : '';

        if (is_null($this->get())) {
            $default = $this->nullable ? 'DEFAULT NULL' : '';
        } else {
            $default = "DEFAULT '".$this->get()."'";
        }
        return  "int({$this->length}) $unsigned $isNull $default";
    }
    
}