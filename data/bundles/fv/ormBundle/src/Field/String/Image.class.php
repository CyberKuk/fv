<?php

    class Field_String_Image extends Field_String_File{

        public $acceptedTypes = "*.jpg;*.jpeg;*.gif;*.png;";

        public function getRealPath( $web ){
            return ( $web ) ? fvSite::$fvConfig->get( "path.upload.web_images" )
                : fvSite::$fvConfig->get( "path.upload.images" );
        }

        public function __toString(){
            return $this->thumb();
        }

        /**
         * Формирует имя тумбочки
         * @param int $wigth
         * @param int $height
         * @return string
         */
        private function thumbName( $width = null, $height = null, $type = null ){
            $info = pathinfo( $this->get() );

            $base = $info["filename"];
            $base .= ( $width ) ? "_w{$width}" : "";
            $base .= ( $height ) ? "_h{$height}" : "";
            $base .= ( $type ) ? "_m{$type}" : "";

            $base .= "." . $info["extension"];
            return $base;
        }

        /** адрес тумбочки */
        public function thumb( $web = true, $width = null, $height = null, $type = null ){
            try{
                return $this->thumbPath( $web, $width, $height, $type );
            }
            catch( EImageNoSourceException $e ){
                if( $web )
                    return fvSite::$fvConfig->get( "path.noImage" );
                else
                    return false;
            }
            catch( EImageException $e ){
                $realPath = $e->getMessage();
                fvMediaLib::createThumbnail( $this->thumbPath( false ),
                                             $realPath,
                                             Array( "width"       => $width,
                                                    "height"      => $height,
                                                    "resize_type" => $type ) );
                return $this->thumb( $web, $width, $height, $type );
            }
        }

        /** путь к тумбочке */
        private function thumbPath( $web = true, $width = null, $height = null, $type = null ){
            if( !$this->checkSource() )
                throw new EImageNoSourceException();

            $dir = $this->getRealPath( $web );
            $dirReal = $this->getRealPath( false );
            $file = $this->thumbName( $width, $height, $type );

            if( !file_exists( $dirReal . $file ) )
                throw new EImageException( $dirReal . $file );

            return $dir . $file;
        }

        public function delete( $fileBase = null ){
            $fileBase = ( $fileBase ) ? $fileBase : $this->get();

            if( parent::delete( $fileBase ) ){
                $filePhrase = pathinfo( $fileBase );
                $files = glob( $this->getRealPath( false ) . $filePhrase["filename"] . "*" );
                if( is_array( $files ) )
                    foreach( $files as $file ){
                        @unlink( $file );
                    }
                return true;
            }

            return false;
        }

        public function getUrl( $width = null, $height = null, $type = null ){
            return
                fvSite::$fvConfig->get( "server_url" ) .
                $this->thumb( true, $width, $height, $type );
        }
    }