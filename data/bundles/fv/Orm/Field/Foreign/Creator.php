<?php

namespace Bundle\fv\Orm\Field\Foreign;

use Bundle\fv\Orm\Root\User;

class Creator extends \Bundle\fv\Orm\Field\Foreign {

    function asMysql() {
        if ( fvSite::$fvSession->getUser() )
            return fvSite::$fvSession->getUser()->getPk();
        else
            return null;
    }

    function isChanged() {
        return is_null( $this->get() );
    }

    function asAdorned() {
        $user = User::getManager()->getByPk( $this->get() );

        if ( $user instanceof User ) {
            if( method_exists( $user, "asAdorned" ) ){
                return $user->asAdorned();
            }
            return $user;
        }
        else
            return "";
    }

}