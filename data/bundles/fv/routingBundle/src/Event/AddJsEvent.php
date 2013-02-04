<?php
namespace Bundle\fv\RoutingBundle\Event;

class AddJsEvent implements \fv\ViewModel\EventInterface{

    private $pathToScript = "";

    public function __construct( $pathToScript ){
        $this->pathToScript = $pathToScript;
    }

    public function getType(){
        return "ADD_JAVASCRIPT";
    }
}
