<?php
namespace Bundle\fv\MetroUIBundle\Component;

use \Bundle\fv\MetroUIBundle\Component\ModulesList as ModulesListScope;
use \Bundle\fv\RoutingBundle\Application\AbstractApplication;
use \Bundle\fv\RoutingBundle\Event;
use \fv\ViewModel\ViewModel;

class ModulesList extends ViewModel{
    function __construct( AbstractApplication $contextApplication, $moduleName ){
        $this->land( "SearchBar", new ModulesListScope\SearchBar() )
             ->assignModules( $contextApplication, $moduleName );
    }

    protected function getLandingPlaces(){
        return [
            "SearchBar",
            "Modules"
        ];
    }

    protected function prepareRender(){
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/metro/accordion.js" ) );
        return $this;
    }

    private function assignModules( AbstractApplication $contextApplication, $moduleName ){
        $modules = new AllModules( $contextApplication, $moduleName, "LeftBar" );
        $this->assignParam( "modules", $modules );
        return $this;
    }


}
