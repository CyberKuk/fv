<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:16
 */

namespace fv\Connection;

use fv\Config\ConfigLoader;
use fv\Config\ConfigurableBuilder;

class ConnectionFactory {

    /** @var \fv\Config\ConfigurableBuilder */
    static private $builder;

    /**
     * @param null $name
     * @return AbstractConnection
     */
    public function getConnection( $name = null ){
        static $instances = array();

        if( ! isset($instances[$name]) )
            $instances[$name] = self::$builder->build($name);

        return $instances[$name];
    }

    public function loadFromConfigFile( $file ){
        self::$builder =
            ConfigurableBuilder::createFromFile( $file )
                ->setDefaultNamespace(__NAMESPACE__)
                ->setInstanceOf(__NAMESPACE__ . "\\AbstractConnection")
                ->setPostfix("Connection");

        return $this;
    }
}
