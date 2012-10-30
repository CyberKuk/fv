<?php

namespace fv\Application;

use fv\Application\Exception\ApplicationLoadException;
use fv\Config\ConfigLoader;

/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 14:30
 */
final class ApplicationLoader {

    protected static $schema = array();

    /**
     * @param $name
     *
     * @return AbstractApplication
     * @throws ApplicationLoadException
     */
    public function getApplication( $name ){
        static $applications;

        if( isset( $applications[$name] ) )
            return $applications[$name];

        if( !isset(self::$schema[$name]) )
            throw new ApplicationLoadException("Unknown application {$name}");

        $schema = self::$schema[$name];
        $namespace = $schema['namespace'];
        $path = rtrim( $schema['path'], DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . "classes";
        if( !is_dir($path) )
            throw new ApplicationLoadException("Path '{$path}' not found to load application {$name}");
        \classLoader\Register::getInstance()->createLoader( $namespace, $path );

        $applicationClass = $namespace . "\\" . "Application";
        $application = new $applicationClass( $schema );

        if( ! $application instanceof AbstractApplication )
            throw new ApplicationLoadException("Application class {$name} must be instance of \\fv\\Application\\AbstractApplication");

        return $applications[$name] = $application;
    }

    public function getApplicationSchemaByNamespace( $namespace ){
        foreach( self::$schema as $appSchema ){
            $statement = "|^" . preg_quote($appSchema['namespace'], "|") . "|";
            if( preg_match( $statement, $namespace ) > 0 )
                return $appSchema;
        }

        return null;
    }

    public function loadFromConfigFile( $file ){
        self::$schema = ConfigLoader::load( $file );
        return $this;
    }
}
