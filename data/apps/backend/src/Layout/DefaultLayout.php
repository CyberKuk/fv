<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 13:37
 */

namespace Application\Backend\Layout;

use Bundle\fv\MetroUIBundle\Layout\MetroLayout;

class DefaultLayout extends MetroLayout {

    private $metaTitle = "Site Title";

    function execute() {
        if( $this->getResponse()->internal->metaTitle ){
            $this->setMetaTitle($this->getResponse()->internal->metaTitle . " " . $this->getMetaTitle());
        }
    }

    public function setMetaTitle( $title ) {
        $this->metaTitle = $title;
        return $this;
    }

    public function getMetaTitle() {
        return $this->metaTitle;
    }

}
