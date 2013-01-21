<?php
namespace fv\Storage;

use fv\Storage\Exception\StorageException;
use fv\Collection\Collection;
use fv\Storage\Exception\StorageInstantiateException;

class File implements Storage {

    private $filename = "tmp/storage";

    public function __construct( $filename = null ){
        if( $filename )
            $this->filename = $filename;

        if( !is_writable($this->filename) )
            throw new StorageInstantiateException("Path {$this->filename} is not writable.");
    }

    public static function build( Collection $config) {
        static $instance;

        if( empty($instance) ){
            $instance = new static( $config->filename );
        }

        return $instance;
    }

    function get( $key ){
        $values = @unserialize( trim( file_get_contents( $this->filename ) ) );
        if ( !is_array( $values ) ){
            return null;
        }

        return $values[$key];
    }

    function set( $key, $value ){
        $handler = fopen( $this->filename, "r+" );

        // @todo: может дожаться unlock'a вместо того чтоботы бросать EXCEPTION?
        if( flock( $handler, LOCK_EX ) ){
            $contents = fread( $handler, filesize( $this->filename ) );
            $values = unserialize( $contents );

            if( !is_array( $values ) )
                $values = Array();

            ftruncate( $handler, 0 );

            $values[$key] = $value;

            fseek( $handler, 0 );
            fwrite( $handler, serialize( $values ) );
            flock( $handler, LOCK_UN );
        } else {
            fclose( $handler );
            throw new StorageException( "Cannot lock file" );
        }

        fclose( $handler );
        return true;
    }
}
