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

        $time = microtime(true);

        for( $i=0; $i<1; $i++ ){
            $n[] = new \SomeEntity();
        }

        $someEntity = new \SomeEntity(); //\SomeEntity::fetch(1);

        $someEntity->setCounter(rand(0,100));

        var_dump( $someEntity->persist() );
        var_dump( $someEntity->getId() );

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
