<?php

namespace fv\Entity\Mixin;

trait Record {

    /**
     * @field
     * @var \fv\Entity\Field\Datetime\Created
     */
    public $ctime;

    /**
     * @field
     * @var \fv\Entity\Field\Datetime\Modified
     */
    public $mtime;

    protected function record(){

    }

}
