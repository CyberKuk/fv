<?php

namespace Application\Frontend\Controller;

use fv\Controller\AbstractController;
use Application\Frontend\Entity\Menu;

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

        $time = microtime(true);

        for( $i=0; $i<1000; $i++ ){
            $n[] = new \SomeEntity();
        }

        var_dump( \SomeEntity::query()->fetch(1) );

        die( microtime(true) - $time );

        $connectionLoader = new \fv\Connection\ConnectionLoader();
        /** @var $connection \fv\Connection\Database\PdoMysql */
        $connection = $connectionLoader->getConnection();

        //var_dump( $connection->query('select "1"')->fetchAll(\PDO::FETCH_ASSOC) );

        return array(
            'name' => $name
        );
    }

}
