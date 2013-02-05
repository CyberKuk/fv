<?php
namespace Bundle\fv\MetroUIBundle\Controller;

use Bundle\fv\MetroUIBundle\Component;
use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\RoutingBundle\Event;

class IndexController extends AbstractController{
    function get(){
        $this
            ->land( "LoggedUserPanel", new Component\LoggedUserPanel() )
            ->land( "DashBoard", new Component\DashBoard( $this->getApplication() ) );
    }

    protected function getLandingPlaces(){
        return [
            "LoggedUserPanel",
            "DashBoard"
        ];
    }

    protected function prepareRender(){
        return $this->triggerEvents();
    }

    protected function triggerEvents(){
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/menu-builder.js" ) );
        $this->triggerEvent( new Event\AddCssEvent( "/css/backend.css" ) );
        return $this;
    }
}
