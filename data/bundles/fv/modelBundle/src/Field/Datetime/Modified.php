<?php
namespace Bundle\fv\ModelBundle\Field\Datetime;


/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 13:08
 */
class Modified extends DateTime {

    function isChanged(){
        return true;
    }

    function asMysql(){
        return $this->set(time())->get();
    }

}
