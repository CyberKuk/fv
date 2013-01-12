<?php
/**
 * User: apple
 * Date: 23.04.12
 * Time: 11:15
 */
class AbstractField_Heap extends Field {

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
