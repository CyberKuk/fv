<?php
/**
 * User: cah4a
 * Date: 19.10.12
 * Time: 17:18
 */

namespace fv\Connection\Driver;

class PdoMysql extends \PDO {

    public function __construct( $dsn, $username = null, $passwd = null, $options = array() ) {
        parent::__construct( $dsn, $username, $passwd, $options );
        $this->setAttribute( self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION );
    }

}
