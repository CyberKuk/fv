<?php
namespace Bundle\fv\MetroUIBundle\Controller;

use Bundle\fv\RoutingBundle\Controller\AbstractController as ParentController;

class AbstractController extends ParentController{
    function get(){
        $modulesConfig = \fv\Config\ConfigurableBuilder::createFromFile( "modules", $this );
        $modulesConfig
            ->setDefaultNamespace( "\\Application\\Backend\\Module" )
            ->setDefaultClass( "\\Bundle\\fv\\MetroUIBundle\\Module\\RootModule" );

        $modules = $modulesConfig->buildAll();

    }
}
