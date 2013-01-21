<?php

class Field_String_Href extends AbstractField_String {

    public function isValid() {
        if ( !strlen( $this->get() ) && $this->nullable )
            return true;

        return self::isHrefValid( $this->get() );
    }

    static function isHrefValid( $href ){
        $pattern = '|^http(s)?://[a-z0-9-]+(.[a-z0-9=+_-]+)*(:[0-9]+)?(/.*)?$|i';
        if ( !preg_match( $pattern, $href ) ) {
            return false;
        }
        return true;
    }

}
