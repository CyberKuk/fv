<?php

namespace Bundle\fv\SessionBundle;

class Bundle extends \fv\Bundle\AbstractBundle {

    /**
     * @return array of dependent bundle namespaces
     */
    function getDependencies() {
        return array( "Bundle\\fv\\ModelBundle" );
    }
}
