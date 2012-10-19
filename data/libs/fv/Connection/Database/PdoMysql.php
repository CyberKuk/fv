<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:18
 */

namespace fv\Connection\Database;

use fv\Connection\Exception\ConnectionException;

class PdoMysql extends \PDO {

    static function build( $schema ){
        foreach( array("host", "user" ) as $param ){
            if( empty($schema[$param] ) )
                throw new ConnectionException("Connection {$schema['class']} must contain '{$param}' param!");
        }

        $host = $schema['host'];
        $dbname = $schema['dbname'];
        $user = $schema['user'];
        $password = $schema['pass'];

        return new self( "mysql:host={$host};dbname={$dbname}", $user, $password );
    }

}
