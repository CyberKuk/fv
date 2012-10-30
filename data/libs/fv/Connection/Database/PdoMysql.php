<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:18
 */

namespace fv\Connection\Database;

use fv\Connection\AbstractConnection;
use fv\Connection\Driver\PdoMysql as Driver;
use fv\Entity\Query\Database\MysqlQuery as Query;

use fv\Connection\Exception\ConnectionException;

class PdoMysql extends AbstractConnection {

    /**
     * @return mixed driver
     */
    protected function connect() {
        $schema = $this->getSchema();

        foreach( array("host", "user" ) as $param ){
            if( empty($schema[$param] ) )
                throw new ConnectionException("Connection {$schema['class']} must contain '{$param}' param!");
        }

        $host = $schema['host'];
        $dbname = $schema['dbname'];
        $user = $schema['user'];
        $password = $schema['pass'];

        return new Driver( "mysql:host={$host};dbname={$dbname}", $user, $password );
    }

    public function createQuery() {
        return new Query( $this );
    }
}
