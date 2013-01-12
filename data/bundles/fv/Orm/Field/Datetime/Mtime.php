<?php

namespace Bundle\fv\Orm\Field\Datetime;

use Bundle\fv\Orm\Field\Datetime;

class Mtime extends Datetime {
    
    function isChanged(){
        return true;
    }
    
}