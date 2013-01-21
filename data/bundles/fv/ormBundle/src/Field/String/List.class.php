<?php

class Field_String_List extends AbstractField_String {
    
     function getEditMethod() {
        return self::EDIT_METHOD_LIST;
     }
    
    function getList( fvRoot $entity ){
      $methodName = "get" . ucfirst($this->key) . "List";
      return $entity->$methodName();
    }
}