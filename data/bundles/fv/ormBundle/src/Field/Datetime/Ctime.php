<?php

namespace OrmBundle\Field\Datetime;

use OrmBundle\Field\Datetime;

class Ctime extends Datetime {
    
    function asMysql(){
        return date('Y-m-d H:i:s');
    }
    
    function isChanged(){
        return is_null( $this->get() );
    }
    
}