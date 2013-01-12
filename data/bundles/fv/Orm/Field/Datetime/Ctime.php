<?php

namespace Bundle\fv\Orm\Field\Datetime;

use Bundle\fv\Orm\Field\Datetime;

class Ctime extends Datetime {
    
    function asMysql(){
        return date('Y-m-d H:i:s');
    }
    
    function isChanged(){
        return is_null( $this->get() );
    }
    
}