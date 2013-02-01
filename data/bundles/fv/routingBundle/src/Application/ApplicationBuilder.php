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
final class ApplicationBuilder {

    private static $loaded = false;
    private static $configName = 'applications';

    /**
     * @param $name
     *
     * @return AbstractApplication
     * @throws ApplicationLoadException
     */
    public function build( $name ){
        static $applications;

        if( isset( $applications[$name] ) )
            return $applications[$name];

        $schema = self::getSchema();

        if( ! $schema->$name )
            throw new ApplicationLoadException("Unknown application {$name}");

        /** @var $schema Collection */
        $schema = $schema->$name;
        $namespace = $schema->namespace->get();
        $path = rtrim( $schema->path->get(), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

        $classesPath = $path . "src";
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

    private static function getSchema() {
        static $config;

        if( !self::$loaded ){
            self::$loaded = true;
            $config = ConfigLoader::load( self::$configName );
        }

        return $config;
    }
}
