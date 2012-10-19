<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:16
 */

namespace fv\Connection;

use fv\Config\ConfigLoader;

use fv\Connection\Exception\ConnectionLoaderException;

class ConnectionLoader {

    static private $schema;

    public function getConnection( $name = 'default' ){
        static $connections;

        if( isset( $applications[$name] ) )
            return $applications[$name];

        if( !isset(self::$schema[$name]) )
            throw new ConnectionLoaderException("Unknown connection {$name}");

        $schema = self::$schema[$name];
        $connectionClass = $schema['class'];

        if( substr( $connectionClass, 0, 1 ) != "\\" )
            $connectionClass = __NAMESPACE__ . "\\" . $connectionClass;

        if( !class_exists($connectionClass) )
            throw new ConnectionLoaderException("Unknown class {$connectionClass}");

        $connection = $connectionClass::build( $schema );

        return $connections[$name] = $connection;
    }

    public function loadFromConfigFile( $file ){
        self::$schema = ConfigLoader::load( $file );
        return $this;
    }
}
