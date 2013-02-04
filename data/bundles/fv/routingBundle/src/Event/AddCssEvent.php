<?php
namespace Bundle\fv\RoutingBundle\Event;

class AddCssEvent implements \fv\ViewModel\EventInterface{

    private $pathToStyleSheet = null;

    public function __construct( $pathToStyleSheet ){
        $this->pathToStyleSheet = $pathToStyleSheet;
    }

    public function getType(){
        return "ADD_CSS";
    }
}
