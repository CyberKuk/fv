<?php

namespace Application\Backend\Layout;

use Bundle\fv\MetroUIBundle\Layout\MetroLayout;

class DefaultLayout extends MetroLayout {

    private $metaTitle = "Site Title";

    function execute() {
        if( $this->getResponse()->internal->metaTitle ){
            $this->setMetaTitle($this->getResponse()->internal->metaTitle->get() . " " . $this->getMetaTitle());
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
