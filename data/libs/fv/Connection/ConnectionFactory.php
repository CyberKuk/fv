<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:16
 */

namespace fv\Connection;

use fv\Config\ConfigLoader;

use fv\Connection\Exception\ConnectionFactoryException;

class ConnectionFactory {

    static private $schema;

    public function getConnection( $name = null ){
        static $connections;

        if( is_null($name) )
            $name = 'default';

        if( isset( $applications[$name] ) )
            return $applications[$name];

        if( !isset(self::$schema[$name]) )
            throw new ConnectionFactoryException("Unknown connection {$name}");

        $schema = self::$schema[$name];
        $connectionClass = $schema['class'];

        if( substr( $connectionClass, 0, 1 ) != "\\" )
            $connectionClass = __NAMESPACE__ . "\\" . $connectionClass;

        if( !class_exists($connectionClass) )
            throw new ConnectionFactoryException("Unknown class {$connectionClass}");

        $connection = new $connectionClass( $schema );

        if( ! $connection instanceof AbstractConnection )
            throw new ConnectionFactoryException("Connection class {$connectionClass} must be instance of \\fv\\Connection\\AbstractConnection");

        return $connections[$name] = $connection;
    }

    public function loadFromConfigFile( $file ){
        self::$schema = ConfigLoader::load( $file );
        return $this;
    }
}
