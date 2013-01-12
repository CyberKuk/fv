<?php

class Field_String_Url extends AbstractField_String {

    public function isValid() {
        $pattern = "/^[a-zA-Z0-9\_\-]{1,255}$/";
        return preg_match( $pattern, $this->value );
    }

}