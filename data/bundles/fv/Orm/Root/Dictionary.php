<?php

namespace Bundle\fv\Orm\Root;

use Bundle\fv\Orm\Root;

use Bundle\fv\Orm\Dictionary as Helper;

/**
 * @property $keyword \Bundle\fv\Orm\Field\String
 * @property $translation \Bundle\fv\Orm\Field\String
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
