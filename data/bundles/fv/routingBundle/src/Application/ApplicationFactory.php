<?php

namespace Bundle\fv\RoutingBundle\Application;

use Bundle\fv\RoutingBundle\Application\Exception\ApplicationLoadException;
use fv\Config\ConfigLoader;
use fv\Collection\Collection;
use fv\View\TemplateRegister;
use fv\Config\ConfigRegister;
use classLoader\Register as ClassLoaderRegister;

/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 14:30
 */
final class ApplicationFactory {

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

        if( ! self::$schema->$name )
            throw new ApplicationLoadException("Unknown application {$name}");

        /** @var $schema Collection */
        $schema = self::$schema->$name;
        $namespace = $schema->namespace->get();
        $path = rtrim( $schema->path->get(), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

        $classesPath = $path . "classes";
        $viewsPath = $path . "views";
        $configsPath = $path . "configs";

        if( !is_dir($classesPath) )
            throw new ApplicationLoadException("Path '{$classesPath}' not found to load application {$name}");

        ClassLoaderRegister::getInstance()->createLoader( $namespace, $classesPath );

        if( is_dir($classesPath) )
            TemplateRegister::registerNamespace( $namespace, $viewsPath );

        if( is_dir($configsPath) )
            ConfigRegister::registerNamespace( $namespace, $configsPath );

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
