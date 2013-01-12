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
        $manager = \Bundle\fv\Orm\Root\Language::getManager();

        $entity = $manager->getByPk(2);

        var_dump($entity->code->get());

        \Bundle\fv\Orm\Query::getDriver();


        $entity->code = rand(10,99);
        $entity->delete();

        return array(
            'name' => $name
        );
    }

}
