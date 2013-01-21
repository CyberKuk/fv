<?php

namespace OrmBundle;

use \fv\Config\ConfigLoader;
use \fv\Collection\Collection;

class Schema {

    /** @var Collection */
    private static $config;

    /**
     * @return \fv\Collection\Collection
     */
    private static function getConfig(){
        if( empty(self::$config) ){
            self::$config = ConfigLoader::load("configs/orm/abstract");
            self::$config->merge(ConfigLoader::load("configs/orm/schema.core"));
            self::$config->merge(ConfigLoader::load("configs/orm/schema"));
        }

        return self::$config;
    }

    public static function get( $path ) {
        return self::getConfig()->get( $path );
    }
}
