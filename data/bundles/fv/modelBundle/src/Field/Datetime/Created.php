<?php

namespace Bundle\fv\ModelBundle\Field\Datetime;

/**
 * User: cah4a
 * Date: 23.10.12
 * Time: 13:08
 */
class Created extends Modified {

    function isChanged(){
        return is_null( $this->get() );
    }

}
