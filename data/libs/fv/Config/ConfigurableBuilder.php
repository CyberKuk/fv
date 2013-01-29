<?php

namespace fv\Config;

use fv\Config\Exception\ConfigurableBuildException as Exception;
use fv\Collection\Collection;

class ConfigurableBuilder {

    /** @var Collection */
    private $config;
    private $defaultNamespace;
    private $defaultClass;
    private $postfix;
    private $instanceOf;

    private function __construct(){}

    public static function createFromFile( $file, $context = null ){
        $builder = new static;
        $builder->setConfig( ConfigLoader::load($file, $context) );
        return $builder;
    }

    public static function createFromCollection( Collection $file ){
        $builder = new static;
        $builder->setConfig( $file );
        return $builder;
    }

    public function build( $key ){
        $config = $this->config->$key;

        if( empty($config) )
            throw new Exception();

        if( $config->class ){
            $className = $config->class->get();
            $className = $className . $this->getPostfix();
        } else
            $className = $this->getDefaultClass();

        if( $className[0] !== '\\')
            $className = $this->getDefaultNamespace() . "\\" . $className;

        if( ! class_exists( $className ) )
            throw new Exception( "Class {$className} not found" );

        if( $instanceof = $this->getInstanceOf() ){
            if( ! is_subclass_of( $className, $instanceof ) && $className !== $instanceof )
                throw new Exception( "{$className} not instance of {$instanceof}" );
        }

        $class = call_user_func( array( $className, "build" ), $config );

        return $class;
    }

    public function buildAll(){
        $result = array();

        foreach( $this->config as $key => $value ){
            $result[$key] = $this->build($key);
        }

        return $result;
    }

    public function setDefaultClass($defaultClass) {
        $this->defaultClass = $defaultClass;
        return $this;
    }

    public function getDefaultClass() {
        return $this->defaultClass;
    }

    /**
     * @return Collection
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return Collection
     */
    private function setConfig( Collection $config ) {
        $this->config = $config;
        return $this;
    }

    public function setDefaultNamespace($defaultNamespace) {
        $this->defaultNamespace = $defaultNamespace;
        return $this;
    }

    public function getDefaultNamespace() {
        return $this->defaultNamespace;
    }

    public function setInstanceOf($instanceOf) {
        $this->instanceOf = $instanceOf;
        return $this;
    }

    public function getInstanceOf() {
        return $this->instanceOf;
    }

    public function setPostfix($postfix) {
        $this->postfix = $postfix;
        return $this;
    }

    public function getPostfix() {
        return $this->postfix;
    }
}
