<?php
namespace Bundle\fv\MetroUIBundle\Layout;

use Bundle\fv\RoutingBundle\Layout\AbstractLayout;
use Bundle\fv\RoutingBundle\Event;
use Bundle\fv\MetroUIBundle\Component;

abstract class MetroLayout extends AbstractLayout{

    private $js = [ ];
    private $css = [ ];

    public function __construct(){
        parent::__construct();

        $this
            ->addEventListener( "ADD_JAVASCRIPT",
                                function ( Event\AddJsEvent $e ){
                                    $this->js[$e->getPriority()][] = $e->getPathToScript();
                                })
            ->addEventListener( "ADD_CSS",
                                function ( Event\AddCssEvent $e ){
                                    $this->css[] = $e->getPathToSheet();
                                })
            ->land( "AppBar", new Component\AppBar() );
    }

    protected function prepareRender(){
        return $this->triggerEvents();
    }

    protected function getLandingPlaces(){
        return [
            "body",
            "AppBar"
        ];
    }

    private function triggerEvents(){
        $this->triggerEvent( new Event\AddJsEvent( "//code.jquery.com/jquery-1.8.3.min.js", Event\AddJsEvent::PRIORITY_HIGH ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/metro/input-control.js" ) );
        $this->triggerEvent( new Event\AddJsEvent( "/javascript/index.js" ) );

        $this->triggerEvent( new Event\AddCssEvent( "/css/metro/modern.css" ) );
        $this->triggerEvent( new Event\AddCssEvent( "/css/metro/modern-responsive.css" ) );
        $this->triggerEvent( new Event\AddCssEvent( "/css/index.css" ) );

        return $this;
    }

    public function getCss(){
        return array_unique( $this->css );
    }

    public function getJs(){
        $jsStack = [];
        foreach( $this->js as $priorityStack ){
            $jsStack = array_merge( $priorityStack, $jsStack );
        }
        return array_unique( $jsStack );
    }

}
