<?php

namespace OrmBundle\Field;

use OrmBundle\Exception\FieldException as Exception;

class Heap extends AbstractField {

    function getEditMethod()
    {
        return self::EDIT_METHOD_INPUT;
    }

    function __construct(array $fieldSchema, $name)
    {
        $fieldSchema["editable"] = false;
        parent::__construct($fieldSchema, $name);
    }

    function isChanged(){
        return false;
    }

    function asInt(){
        return (int)$this->get();
    }

    function asBool(){
        return (bool)$this->get();
    }

    function getSQlPart() {
        throw new Exception('Can not generate SQL for heap field');
    }

}
