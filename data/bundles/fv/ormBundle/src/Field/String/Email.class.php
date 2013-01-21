<?php

class Field_String_Email extends AbstractField_String {
    public function isValid() {
        $pattern = "/^[a-z0-9_\-\.]+@[a-z_\-\.]+\.[a-z]{2,3}$/i";
        return preg_match($pattern, $this->value);
    }

}