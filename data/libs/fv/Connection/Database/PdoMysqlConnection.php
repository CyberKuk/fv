<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:18
 */

namespace fv\Connection\Database;

use fv\Connection\AbstractConnection;
use fv\Connection\Driver\PdoMysqlDriver as Driver;
use fv\Connection\Exception\ConnectionException;

class PdoMysqlConnection extends AbstractConnection {

    /**
     * @throws \fv\Connection\Exception\ConnectionException
     * @return Driver
     */
    protected function connect() {
        $schema = $this->getSchema();

        foreach( array( "host", "dbname", "user" ) as $param ){
            if( ! $schema->$param )
                throw new ConnectionException("Connection {$schema->class->get()} must contain '{$param}' param!");
        }

        $host = $schema->host->get();
        $dbname = $schema->dbname->get();
        $user = $schema->user->get();
        $password = null;

        if( $schema->pass )
            $password = $schema->pass->get();

        return new Driver( "mysql:host={$host};dbname={$dbname}", $user, $password );
    }

}
