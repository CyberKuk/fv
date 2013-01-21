<?php

namespace OrmBundle\Root;

use OrmBundle\Root;
use OrmBundle\Query;

/**
 * @method static \OrmBundle\Root\LanguageManager getManager()
 */
class Language extends Root
{
    public static function getEntity(){ return __CLASS__; }

    function isSelected(){
        return $this->code == LanguageManager::$languageCode;
    }

    function save( $logging = false ){
        parent::save($logging);
        self::getManager()->reloadCache();
    }

    function delete(){
        parent::delete();
        self::getManager()->reloadCache();
    }

    /* NOT USED ANY MORE!

    function saveLang(){
        $result = parent::save();
        if( $result && $this->is_default ){
            $sql = "update " . Language::getManager()
                ->getTableName() . " SET is_default = 0 where id <> " . $this->getPk();

            Query::getDriver()->query( $sql );
        }
        return $result;
    }

    function validateName( $value ){
        return $this->setValidationResult( "name",
            ( strlen( $value ) ? true : false ),
            "Поле должно быть заполненным" );
    }
    function validateCode( $value ){
        $valid = ( strlen( $value ) > 0 );
        if( !$valid )
            $message = "поле не должно быть пустым";
        else{
            $count = Language::getManager()->getCount( " code = '{$value}' " );

            if( !$this->isNew() )
                $count = $count - 1;

            if( $count <= 0 )
                $valid = true;
            else{
                $message = "поле должно быть уникальным";
                $valid = false;
            }
        }

        $this->setValidationResult( "code", $valid, $message );
        return $valid;
    } */
}