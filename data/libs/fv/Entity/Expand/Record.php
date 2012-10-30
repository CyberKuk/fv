<?php

namespace fv\Entity\Expand;

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

}
