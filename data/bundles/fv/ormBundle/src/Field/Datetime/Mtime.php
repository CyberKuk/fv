<?php

namespace OrmBundle\Field\Datetime;

use OrmBundle\Field\Datetime;

class Mtime extends Datetime {
    
    function isChanged(){
        return true;
    }
    
}