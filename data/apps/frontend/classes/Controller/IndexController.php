<?php

namespace Application\Frontend\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController;

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

        //$ent = new \SomeEntity();
        ////var_dump( $ent );
        //$ent->foreign->set(rand(1,2));
        //var_dump( $ent->foreign->get() );
        //$ent->persist();

        //foreach( \SomeEntity::select()->fetchAll() as $pk => $ent ){
        //    var_dump($pk, $ent->foreign->get());
        //}

        /** @var $other \OtherEntity */
        $other = \OtherEntity::fetch(1);

        var_dump($other->somes->get());

        die;
        /** @var $ents \SomeEntity[] */
        $ents = \SomeEntity::select()->fetchAll();

        var_dump($ents);

        if( count($ents) == 2 ){
            foreach( $ents as $ent ){
                $ent->remove();
            }
            print "Removed all! <br/>";
            $ents = \SomeEntity::select()->fetchAll();
            print "total: " . count($ents) . " records";
        } elseif( count($ents) == 1 ){
            $ent = new \SomeEntity();
            $ent->persist();
            print "Persist one more 1 record <br/>";
            $ents = \SomeEntity::select()->fetchAll();
            print "total: " . count($ents) . " records";
        } elseif( count($ents) == 0 ){
            $ent = new \SomeEntity();
            $ent->persist();
            print "Persist 1 record <br/>";
            $ents = \SomeEntity::select()->fetchAll();
            print "total: " . count($ents) . " records";
        }

        die;



        //$factory = new \Bundle\fv\Storage\StorageFactory;
        //$storage = $factory->get("memcache");

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
