<?php

namespace Application\Frontend\Controller;

use fv\Controller\AbstractController;

/**
 * User: cah4a
 * Date: 10.09.12
 * Time: 18:42
 */
class IndexController extends AbstractController {

    function getHello(){
        return "Hello";
    }

    function get( $name = "default world" ){

        $connectionLoader = new \fv\Connection\ConnectionLoader();
        /** @var $connection \fv\Connection\Database\PdoMysql */
        $connection = $connectionLoader->getConnection();

        //var_dump( $connection->query('select "1"')->fetchAll(\PDO::FETCH_ASSOC) );

        return array(
            'name' => $name
        );
    }

}
