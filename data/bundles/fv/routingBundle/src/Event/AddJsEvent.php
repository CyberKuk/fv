<?php
namespace Bundle\fv\RoutingBundle\Event;

class AddJsEvent implements \fv\ViewModel\EventInterface{

    const PRIORITY_HIGH = 1;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_LOW = 5;

    private $pathToScript = "";
    private $priority = self::PRIORITY_NORMAL;

    public function __construct( $pathToScript, $priority = self::PRIORITY_NORMAL ){
        $this->pathToScript = $pathToScript;
        $this->priority = $priority;
    }

    public function getType(){
        return "ADD_JAVASCRIPT";
    }

    public function getPathToScript(){
        return $this->pathToScript;
    }

    public function getPriority(){
        return $this->priority;
    }
}
