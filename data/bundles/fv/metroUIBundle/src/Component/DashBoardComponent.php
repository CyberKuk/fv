<?php
namespace Bundle\fv\MetroUIBundle\Component;

use \Bundle\fv\RoutingBundle\Application\AbstractApplication;

class DashBoardComponent extends \fv\ViewModel\ViewModel {

    /**
     * @var AbstractApplication
     */
    private $application;

    function __construct( AbstractApplication $application ){
        $this->application = $application;

        $modulesConfig = \fv\Config\ConfigurableBuilder::createFromFile( "modules", $application )
            ->setDefaultNamespace( $application->getNamespace() . "Module\\" )
            ->setDefaultClass( "\\Bundle\\fv\\MetroUIBundle\\Module\\RootModule" );

        /**
         * @var $modules \Bundle\fv\MetroUIBundle\Module\RootModule[]
         */
        $modules = $modulesConfig->buildAll();

        $groups = Array();
        foreach( $modules as $module ){
            $groups[$module->getGroup()][] = $module;
            $this->land( "Modules", $module );
        }

        $this->assignParam( "modules", $groups );
    }

    protected function getLandingPlaces(){
        return [ "Modules" ];
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Application\AbstractApplication
     */
    public function getApplication(){
        return $this->application;
    }
}
