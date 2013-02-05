<?php
namespace Bundle\fv\MetroUIBundle\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\MetroUIBundle\Component;
use Bundle\fv\RoutingBundle\Event;

class EditController extends AbstractController{
    function get(){
        $this->land( "ModulesList", new Component\ModulesList() );
        $this->land( "EntityList", new Component\EntityList() );
        $this->land( "EntityEdit", new Component\EntityEdit() );
    }

    protected function getLandingPlaces(){
        return [
            "ModulesList",
            "EntityList",
            "EntityEdit"
        ];
    }

    protected function prepareRender(){
        $this->triggerEvents();
        return $this;
    }

    private function triggerEvents(){
        $this->triggerEvent( new Event\AddCssEvent( "/css/module.css" ) );


        $this->triggerEvent( new Event\AddJsEvent( "/javascript/metro/pagecontrol.js" ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/metro/input-control.js" ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/metro/dropdown.js" ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/module.js" ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/index.js" ) );

        return $this;
    }
}
