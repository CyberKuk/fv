<?php
namespace Bundle\fv\MetroUIBundle\Component;

use \Bundle\fv\RoutingBundle\Application\AbstractApplication;

class DashBoard extends \fv\ViewModel\ViewModel {

    /**
     * @var AbstractApplication
     */
    private $application;

    function __construct( AbstractApplication $application ){
        $this->application = $application;
        $this->assignParam( "modules", new AllModules( $this->application, null, "Dashboard" ) );
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
