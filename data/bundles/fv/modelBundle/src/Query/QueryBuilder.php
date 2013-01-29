<?php

namespace Bundle\fv\ModelBundle\Query;

use Bundle\fv\ModelBundle\Exception\QueryInstantiateException;
use fv\Connection\AbstractConnection;
use fv\Connection\ConnectionFactory;

class QueryBuilder {

    static $reflects = array(
        "fv\\Connection\\Database\\PdoMysqlConnection" => "Bundle\\fv\\ModelBundle\\Query\\Database\\MysqlQuery"
    );

    /**
     * @param $connection
     * @return \Bundle\fv\ModelBundle\Query\Database\DatabaseQuery|AbstractQuery
     * @throws \Bundle\fv\ModelBundle\Exception\QueryInstantiateException
     */
    static function getQueryToConnection( $connection ){
        if( is_string($connection) ){
            $connectionFactory = new ConnectionFactory;
            $connection = $connectionFactory->getConnection( $connection );
        }

        if( is_null($connection) )
            throw new QueryInstantiateException("Connection could not be null");


        if( ! $connection instanceof AbstractConnection ){
            $class = get_class($connection);
            throw new QueryInstantiateException("Strange connection class {$class} ");
        }

        foreach( self::$reflects as $class => $toClass ){
            if( $connection instanceof $class ){
                return new $toClass( $connection );
            }
        }

        $class = get_class($connection);

        throw new QueryInstantiateException("Could not instantiate query for connection type {$class}");
    }

    public static function registerReflect( $connectionClassName, $queryClassName ){
        if( ! is_subclass_of($connectionClassName, "fv\\Connection\\AbstractConnection") ){
            throw new QueryInstantiateException("Connection class name must be subclass of fv\\Connection\\AbstractConnection");
        }

        if( ! is_subclass_of($queryClassName, "Bundle\\fv\\ModelBundle\\Query\\AbstractQuery") ){
            throw new QueryInstantiateException("Query class name must be subclass of Bundle\\fv\\ModelBundle\\Query\\AbstractQuery");
        }

        self::$reflects[$connectionClassName] = $queryClassName;
    }

}
