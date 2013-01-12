<?php

namespace Bundle\fv\Orm\Root;

use Bundle\fv\Orm\Root;

class Language extends Root
{
    public static function getEntity(){ return __CLASS__; }

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
    }

    function saveLang(){
        $result = parent::save();
        if( $result && $this->is_default ){
            $sql = "update " . Language::getManager()
                ->getTableName() . " SET is_default = 0 where id <> " . $this->getPk();
            fvSite::$pdo->query( $sql );
        }
        return $result;
    }

    function isSelected(){
        return $this->code == fvSite::$fvConfig->getCurrentLang();
    }


    //function save( $logging = false ){
    //    parent::save($logging);
    //    $this->getManager()->reloadCache();
    //}
//
    //function delete(){
    //    parent::delete();
    //    $this->getManager()->reloadCache();
    //}
}
