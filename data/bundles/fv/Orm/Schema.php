<?php

namespace Bundle\fv\Orm;

use \fv\Config\ConfigLoader;
use \fv\Collection;

class Schema {

    /** @var Collection */
    private static $config;

    /**
     * @return \fv\Collection
     */
    private static function getConfig(){
        if( empty(self::$config) ){
            self::$config = ConfigLoader::loadCollection("configs/orm/abstract");
            self::$config->merge(ConfigLoader::loadCollection("configs/orm/schema.core"));
            self::$config->merge(ConfigLoader::loadCollection("configs/orm/schema"));
        }

        return self::$config;
    }

    public static function get( $path ) {
        return self::getConfig()->get( $path );
    }
}
