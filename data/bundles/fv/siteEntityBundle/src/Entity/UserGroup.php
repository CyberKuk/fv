<?php
namespace Bundle\fv\SiteEntityBundle\Entity;

use Bundle\fv\ModelBundle\AbstractModel;
use Bundle\fv\ModelBundle\Mixin;

class UserGroup extends AbstractModel{

    use Mixin\Record;
    use Mixin\Active;

    /**
     * @field ( autoincrement=true )
     * @var \Bundle\fv\ModelBundle\Field\Int
     */
    protected $id;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\String
     */
    public $name;
}
