<?php

namespace Bundle\fv\Orm\Field;

class Rich extends Textarea {
    
    function getEditMethod() {
        return self::EDIT_METHOD_RICH;
    }
}