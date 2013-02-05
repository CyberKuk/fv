<?php
namespace Bundle\fv\SiteEntityBundle\Mixin;

trait UserRecord{
    /**
     * @field ( modelName=Bundle\fv\SiteEntityBundle\Entity\User, key=foreign )
     * @var \Bundle\fv\SiteEntityBundle\Field\Relation\Foreign\Creator
     */
    public $creator;

    /**
     * @field ( modelName=Bundle\fv\SiteEntityBundle\Entity\User, key=foreign )
     * @var \Bundle\fv\SiteEntityBundle\Field\Relation\Foreign\Modifier
     */
    public $modifier;
}