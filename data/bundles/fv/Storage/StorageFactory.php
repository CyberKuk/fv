<?php

namespace Bundle\fv\Storage;

use fv\Config\ConfigLoader;
use fv\Collection;
use Bundle\fv\Storage\Exception\StorageFactoryException;
use Bundle\fv\Storage\Exception\StorageInstantiateException;

class StorageFactory {

    /** @var Collection */
    private static $schema;

    private static $exception = array();

    /** @return Collection */
    public function getConfig(){
        if( empty(self::$schema) ){
            self::$schema = ConfigLoader::loadCollection( 'configs/storage' );
        }

        return self::$schema;
    }

    public function get( $cacheSystemName ){
        if( isset( self::$exception[$cacheSystemName] ) )
            throw self::$exception[$cacheSystemName];

        $config = $this->getConfig()->$cacheSystemName;

        if( ! $config )
            throw new StorageFactoryException("Storage System {$cacheSystemName} not defined!");

        $className = (string)$config->class;

        if( empty($className) )
            $className = ucfirst( $cacheSystemName );

        if( $className[0] != "\\" )
            $className = __NAMESPACE__ . "\\" . $className;

        try{
            if( !class_exists($className) )
                throw new StorageFactoryException("Class {$className} for cache system {$cacheSystemName} not defined!");

            return call_user_func( array( $className, "build" ), $config );
        } catch( StorageInstantiateException $e ){
            self::$exception[$cacheSystemName] = $e;

            if( $config->fallback ){
                return $this->get( $config->fallback );
            }

            throw $e;
        }
    }

}
