<?php
namespace Bundle\fv\MetroUIBundle\Component;

class DashBoardComponent extends \fv\ViewModel\ViewModel {
    function __construct( \Bundle\fv\RoutingBundle\Application\AbstractApplication $application ){

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
        }

        $this->assignParam( "modules", $groups );
    }
}
