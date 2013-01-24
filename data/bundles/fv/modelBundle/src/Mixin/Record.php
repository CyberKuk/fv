<?php

namespace Bundle\fv\ModelBundle\Mixin;

trait Record {

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\Datetime\Created
     */
    public $ctime;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\Datetime\Modified
     */
    public $mtime;

}
