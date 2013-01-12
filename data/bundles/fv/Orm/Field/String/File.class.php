<?php

class Field_String_File extends AbstractField_String{
    const NO_SOURCE = -1;

    public $acceptedTypes = "*.*;";
    protected $oldFile = null;

    function getEditMethod(){
        return self::EDIT_METHOD_UPLOAD;
    }

    public function getToken(){
        return md5( $this->name );
    }

    /**
     * @param bool $web
     *
     * @return string
     */
    public function getTemporalPath( $web = true ){
        return ( $web ) ? fvSite::$fvConfig->get( "path.upload.web_temporal" ) :
            fvSite::$fvConfig->get( "path.upload.temporal" );
    }

    public function getTemporalFile( $web = false ){
        $path = $this->getTemporalPath( $web ) . $this->get();
        return $path;
    }

    public function getRealPath( $web ){
        return ( $web ) ? fvSite::$fvConfig->get( "path.upload.web_files" ) :
            fvSite::$fvConfig->get( "path.upload.files" );
    }

    public function getPath( $web = false ){
        $directory = $this->getRealPath( $web ) . $this->get();
        return $directory;
    }

    public function upload(){
        if( !$this->isChanged() )
            return true;
        else{
            if( !is_null( $this->oldFile ) ){
                $this->delete( $this->oldFile );
                $this->oldFile = null;
            }
        }

        if( !file_exists( $this->getTemporalFile() ) )
            throw new EFieldError( "Temporal file is not exists!", null, null );

        $fileExtention = strtolower( substr( strrchr( $this->get(), "." ), 1 ) );
        $fileBaseName = $fileName = substr( basename( $this->get() ), 0, -strlen( $fileExtention ) - 1 );

        for( $i = 1; true; $i++ ){



            if( $this->checkSource( $this->get() ) ){
                $this->set( $fileBaseName. "_" . $i . "." . $fileExtention );
               // $dfg = $this->getTemporalPath( false ) . $fileName . "." . $fileExtention;
                //$dfg2 = $this->getTemporalFile();
                rename( $this->getTemporalPath( false ) . $fileName . "." . $fileExtention , $this->getTemporalPath( false ) . $this->get() );
                $fileExtention = strtolower( substr( strrchr( $this->get(), "." ), 1 ) );
                $fileName = substr( basename( $this->get() ), 0, -strlen( $fileExtention ) - 1 );
            }
            else{
                break;
            }
        }

        return rename( $this->getTemporalFile(), $this->getPath() );
    }

    /**
     * Проверяет существует ли файл
     * @return boolean
     */
    protected function checkSource( $fileBase = null ){
        if( is_null( $fileBase ) ){
            $fileBase = $this->get();
            if( !$fileBase )
                return false;
        }

        if( !file_exists( $this->getRealPath( false ) . $fileBase ) )
            return false;

        return true;
    }

    public function delete( $fileBase = null ){
        $fileBase = ( $fileBase ) ? $fileBase : $this->get();
        if( $this->checkSource( $fileBase ) )
            return unlink( $this->getRealPath( false ) . $fileBase );
        return false;
    }

    public function set( $value ){
        $this->oldFile = $this->get();
        parent::set( $value );
    }

}