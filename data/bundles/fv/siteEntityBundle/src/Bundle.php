<?php
namespace Bundle\fv\SiteEntityBundle;

use \fv\Bundle\AbstractBundle;

class Bundle extends AbstractBundle{
    /**
     * @return array of dependent bundle namespaces
     */
    public function getDependencies(){
        return array( "Bundle\\fv\\ModelBundle" );
    }
}
