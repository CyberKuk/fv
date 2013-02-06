<?php
namespace Bundle\fv\MetroUIBundle\Component;

use \fv\ViewModel\Statable;
use \Bundle\fv\RoutingBundle\Application\AbstractApplication;
use \Bundle\fv\MetroUIBundle\Module\AbstractModule;

class AllModules{
    use Statable;

    public $moduleGroups;

    public function __construct( AbstractApplication $contextApplication, $selectedModule = null, $state ){
        $this
            ->setState( $state )
            ->setContext( $contextApplication );

        $modules =
            \fv\Config\ConfigurableBuilder::createFromFile( "modules", $contextApplication )
                ->setDefaultNamespace( $contextApplication->getNamespace() . "Module\\" )
                ->setDefaultClass( "\\Bundle\\fv\\MetroUIBundle\\Module\\RootModule" )
                ->buildAll();

        foreach( $modules as $module ){
            $this->addModule( $module, $selectedModule, $contextApplication );
        }
    }

    private function addModule( $module, $selectedModule){
        $this
            ->getModuleGroup( $module )
            ->addModule( $module, $selectedModule );

        return $this;
    }

    /**
     * @param \Bundle\fv\MetroUIBundle\Module\AbstractModule $module
     * @return ModuleGroup
     */
    private function getModuleGroup( AbstractModule $module ){
        if( !isset( $this->moduleGroups[md5( $module->getGroup() )] ) ){
            $moduleGroup = new ModuleGroup( $module->getGroup(), $this->getState() );
            $moduleGroup->setContext( $this->getContext() );

            $this->moduleGroups[md5( $module->getGroup() )] = $moduleGroup;
        }

        return $this->moduleGroups[md5( $module->getGroup() )];
    }

    public function getStates(){
        return [
            "Dashboard", "LeftBar"
        ];
    }
}
