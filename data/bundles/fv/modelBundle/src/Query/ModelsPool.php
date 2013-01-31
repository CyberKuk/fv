<?php

namespace Bundle\fv\ModelBundle\Query;

use Bundle\fv\ModelBundle\AbstractModel;

class ModelsPool {

    private static $refs;

    /**
     * @param \Bundle\fv\ModelBundle\AbstractModel $model
     * @return \Bundle\fv\ModelBundle\AbstractModel
     */
    public static function persist( AbstractModel $model ){
        $keys = $model->getPrimaryFields();

        if( empty($keys) )
            return $model;

        $root = &self::$refs[get_class($model)];
        foreach( $keys as $field ){
            $root = &$root[$field->get()];
        }

        if( ! isset( $root ) ){
            $root = $model;
        }

        return $root;
    }

    /**
     * @param $modelName
     * @param $map
     * @return \Bundle\fv\ModelBundle\AbstractModel
     */
    public static function create( $modelName,  $map) {
        /** @var $model \Bundle\fv\ModelBundle\AbstractModel */
        $model = new $modelName;

        foreach( $model->getFields() as $key => $field ){
            if( isset( $map[$key] ) ){
                $field->fromMysql($map[$key]);
                unset( $map[$key] );
            }
        }

        $model = self::persist($model);

        foreach( $map as $key => $value ){
            // @todo: save heap fields to Model
        }

        return $model;
    }

}
