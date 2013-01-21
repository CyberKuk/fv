<?php

namespace OrmBundle\Root;

use OrmBundle\Root;

use OrmBundle\Dictionary as Helper;

/**
 * @property $keyword \OrmBundle\Field\String
 * @property $translation \OrmBundle\Field\String
 */
class Dictionary extends Root
{
    public static function getEntity(){ return __CLASS__; }

    function save($logging = true) {
        parent::save($logging);

        Helper::getInstance()->dropCache();
    }

    function delete() {
        parent::delete();

        Helper::getInstance()->dropCache();
    }
}
