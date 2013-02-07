<?php
namespace fv\Storage;

use fv\Storage\Exception\StorageException;
use fv\Collection\Collection;

class SharedMemory implements Storage {

    private $key = 0xff3;
    private $flags = "c";
    private $mode = 0666;
    private $size = 2048;

    public function __construct( $key = null, $flags = null, $mode = null, $size = null ){
        if( $key )
            $this->key = $key;

        if( $flags )
            $this->flags = $flags;

        if( $mode )
            $this->mode = $mode;

        if( $size )
            $this->size = $size;
    }

    public static function build( Collection $config) {
        static $instance;

        if( !function_exists("shmop_open") )
            throw new \fv\Storage\Exception\StorageInstantiateException("Function shmop_open is undefined. Is PHP_SHMOD extension enabled?");

        if( empty($instance) ){
            $instance = new static( $config->key, $config->flags, $config->mode, $config->size );
        }

        return $instance;
    }

    public function get( $key ){
        $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
        $data = @unserialize( shmop_read( $memoryHandler, 0, $this->size ) );
        shmop_close( $memoryHandler );

        if( is_array( $data ) && isset( $data[$key] ) )
            return $data[$key];

        return null;
    }

    public function set( $key, $value ){
        $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
        $data = shmop_read( $memoryHandler, 0, $this->size );
        $arr = @unserialize( $data );

        if( !is_array( $arr ) ){
            $arr = Array();
        }

        $arr[$key] = $value;

        $data = serialize($arr);

        // NOT mb_strlen cuz byte read/write !
        if( strlen($data) > $this->size )
            throw new StorageException("Shared memory oversized!");

        shmop_write( $memoryHandler, $data, 0 );
        shmop_close( $memoryHandler );

        return true;
    }

    public function delete($key)
    {
        $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
        $data = shmop_read( $memoryHandler, 0, $this->size );
        $arr = @unserialize( $data );

        if( !is_array( $arr ) || !array_key_exists($key, $arr) ){
            shmop_close( $memoryHandler );
            return false;
        }

        unset($arr[$key]);

        $data = serialize($arr);

        // NOT mb_strlen cuz byte read/write !
        if( strlen($data) > $this->size )
            throw new StorageException("Shared memory oversized!");

        shmop_write( $memoryHandler, $data, 0 );
        shmop_close( $memoryHandler );

        return true;
    }


}
