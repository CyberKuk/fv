<?php

class Field_String_Manual extends Field_String_File{

    public function isValid(){
        if( mime_content_type( $this->getTemporalFile() ) != 'application/pdf' ){
            return false;
        }

        return parent::isValid();
    }

    public function getRealPath( $web ){
        return ( $web ) ? fvSite::$fvConfig->get( "path.upload.web_manual_files" ) :
            fvSite::$fvConfig->get( "path.upload.manual_files" );
    }
}