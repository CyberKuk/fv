<?php
namespace Bundle\fv\MetroUIBundle\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController;
use Bundle\fv\RoutingBundle\Event;
use Bundle\fv\MetroUIBundle\Component;

class EditController extends AbstractController{
    /**
     * @var \Bundle\fv\MetroUIBundle\Module\AbstractModule
     */
    private $module;

    function get( $moduleName ){
        $this
            ->extractModule( $moduleName )
            ->land( "ModulesList", new Component\ModulesList( $this->getApplication(), $moduleName ) )
            ->land( "EntityList", new Component\EntityList( $this->module ) )
            ->land( "EntityEdit", new Component\EntityEdit() );
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

    private function extractModule( $moduleName ){
        /**
         * @var $modules \Bundle\fv\MetroUIBundle\Module\RootModule[]
         */
        $modules =
            \fv\Config\ConfigurableBuilder::createFromFile( "modules", $this->getApplication() )
                ->setDefaultNamespace( $this->getApplication()->getNamespace() . "Module\\" )
                ->setDefaultClass( "\\Bundle\\fv\\MetroUIBundle\\Module\\RootModule" )
                ->buildAll();

        foreach( $modules as $module ){
            if( $module->getSystemName() == $moduleName ){
                $this->module = $module;
            }
        }

        if( !$this->module instanceof \Bundle\fv\MetroUIBundle\Module\AbstractModule ){
            throw new \RuntimeException( "Cannot find module assigned to „{$moduleName}“." );
        }

        return $this;
    }
}
