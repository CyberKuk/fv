<?php
namespace Bundle\fv\MetroUIBundle\Component;

use \Bundle\fv\MetroUIBundle\Module\AbstractModule;
use \fv\ViewModel\Statable;

class ModuleGroup{
    use Statable;

    public $modulesList = [ ];
    public $isActive = false;
    public $systemName = "";

    function __construct( $systemName, $state ){
        $this->setState( $state );
        $this->systemName = $systemName;
    }

    public function addModule( AbstractModule $module, $selectedModule ){
        if( $module->getSystemName() == $selectedModule ){
            $this->isActive = true;
            $module->isActive = true;
        }

        $module->setState( $this->getState() );
        $module->setContext( $this->getContext() );
        $this->modulesList[] = $module;
        return $this;
    }

    public function getStates(){
        return [
            "Dashboard",
            "LeftBar"
        ];
    }
}
