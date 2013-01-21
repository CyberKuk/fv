<?php

class Field_String_Password extends AbstractField_String {

    function isValid() {

        if ( !preg_match( "/^[a-z_\s0-9]{4,}$/i", $this->get() ) ) {
            $this->errorType = 1;
            return false;
        }


        $m = fvRequest::getInstance()->getRequestParameter( "c", "array",
                array( ) );

        if ( isset( $m[ 'password1' ] ) ) {
            $confirmPassword = md5( $m[ 'password1' ] );

            if ( $confirmPassword != $this->get() ) {
                $this->errorType = 2;
                return false;
            }
        }
        return true;
    }

    function getEditMethod() {
        return self::EDIT_METHOD_INPUT;
    }

}