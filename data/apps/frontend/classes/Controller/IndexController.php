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
        $factory = new \Bundle\fv\Storage\StorageFactory;
        $storage = $factory->get("memcache");

        //for( $i = 0;  $i < 2; $i++ ){
        //    $obj = new \Bundle\fv\Orm\Root\Language;
        //    $storage->set("s" . $i,  $obj);
        //}
//
        //for( $i = 0;  $i < 2; $i++ ){
        //    var_dump( $storage->get("s" . $i) );
        //}

        /*
        $manager = \Bundle\fv\Orm\Root\Language::getManager();

        $entity = $manager->getByPk(2);

        var_dump($entity->code->get());

        \Bundle\fv\Orm\Query::getDriver();


        $entity->code = rand(10,99);
        $entity->delete();
        */

        return array(
            'name' => $name
        );
    }

}
