<?php

namespace classLoader;

/**
 * User: cah4a
 * Date: 12.09.12
 * Time: 19:51
 */
class Register {

    private $loaders = array();

    /**
     * @static
     * @param string $namespace
     * @param string $path
     * @param bool $register
     * @return Loader
     */
    static function createLoader( $namespace, $path, $register = true ){
        $loader = new Loader( $namespace, $path );

        if( $register )
            $loader->register();

        self::getInstance()->addLoader( $loader );

        return $loader;
    }

    final private function __construct(){}

    /**
     * @return Register
     */
    final public static function getInstance(){
        static $instance;

        if( !$instance )
            $instance = new self;

        return $instance;
    }

    /**
     * @param Loader $loader
     * @return Register
     * @throws \Exception
     */
    function addLoader( Loader $loader ){
        if( isset($this->loaders[$loader->getNamespace()]) )
            throw new Exception("Namespace already initialized!");

        $this->loaders[$loader->getNamespace()] = $loader;

        return $this;
    }

    /**
     * @param $namespace
     * @return \ClassLoader\Loader
     * @throws \Exception
     */
    function getLoader( $namespace ){
        if( ! isset($this->loaders[$namespace]) )
            throw new Exception("Namespace is not defined!");

        return $this->loaders[$namespace];
    }

    /**
     * @param $namespace
     * @return Register
     */
    function removeLoader( $namespace ){
        $this
            ->getLoader( $namespace )
            ->unregister();

        unset( $this->loaders[$namespace] );

        return $this;
    }

}
