<?php
    /**
     * @author Iceman
     * @since 19.11.12 14:40
     */
    class Field_String_Phone extends AbstractField_String {

        public function set( $value ){
            if( empty($value) )
                parent::set( null );
            else{
                $value = preg_replace( '/[^\d\+]/', '', $value);
                if( empty( $value ) )
                    $value = "0";
                parent::set( $value );
            }
        }

        public function isValid() {
            $value = $this->get();
            if( is_null($value) ){
                return parent::isValid();
            }

            $pattern = "/^\+?3?8?0\d{9}$/i";
            return preg_match($pattern, $value);
        }
    }
