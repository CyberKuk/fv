<?php
namespace Bundle\fv\MetroUIBundle\Controller;

use Bundle\fv\MetroUIBundle\Component;
use Bundle\fv\RoutingBundle\Controller\AbstractController;

class IndexController extends AbstractController{
    function get(){
        $this->land( "LoggedUserPanel", new Component\LoggedUserPanelComponent() );
        $this->land( "DashBoard", new Component\DashBoardComponent( $this->getApplication() ) );
    }

    protected function getLandingPlaces(){
        return [
            "LoggedUserPanel",
            "DashBoard"
        ];
    }
}
