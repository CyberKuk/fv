<?php
namespace Bundle\fv\SessionBundle\Entity;

use Bundle\fv\ModelBundle\AbstractModel;
//use Bundle\fv\ModelBundle\Mixin;
//use Bundle\fv\SiteEntityBundle\Mixin as ExtendedMixin;

/**
 * @primaryIndex (sessionId)
 * @table Session
 */
class Session extends AbstractModel{

    //use Mixin\Record;
    //use Mixin\Active;

    /**
     * @field (autoincrement=false)
     * @var \Bundle\fv\ModelBundle\Field\Primary
     */
    public $sessionId;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\Text
     */
    public $data;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\Datetime\Modified
     */
    public $mtime;

    static function getEntity(){
        return __CLASS__;
    }
}
