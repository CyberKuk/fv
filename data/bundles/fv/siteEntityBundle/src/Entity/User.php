<?php
namespace Bundle\fv\SiteEntityBundle\Entity;

use Bundle\fv\ModelBundle\AbstractModel;
use Bundle\fv\ModelBundle\Mixin;
use Bundle\fv\SiteEntityBundle\Mixin as ExtendedMixin;

class User extends AbstractModel{

    use Mixin\Record;
    use Mixin\Active;

    /**
     * @field ( autoincrement=true )
     * @var \Bundle\fv\ModelBundle\Field\Int
     */
    protected $id;

    /**
     * @field ( modelName=Bundle\fv\SiteEntityBundle\Entity\UserGroup, key=foreign )
     * @var \Bundle\fv\ModelBundle\Field\Relation\Constraint
     */
    public $userGroup;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\String
     */
    public $name;

    /**
     * @field
     * @var \Bundle\fv\ModelBundle\Field\String
     */
    public $surname;

    static function getEntity(){
        return __CLASS__;
    }
}
