<?php

namespace fv\ViewModel;

use fv\View\AbstractView;
use fv\ViewModel\Exception\ViewModelException;

class ViewModel {

    use Viewlet;

    /** @var ViewModel|null */
    private $owner;

    /**
     * @param ViewModel $owner
     * @return ViewModel
     */
    public function setOwner( ViewModel $owner) {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return ViewModel|null
     */
    public function getOwner() {
        return $this->owner;
    }

    private $eventListeners = array();

    protected function addEventListener( $eventType, Callable $function ){
        $this->eventListeners[$eventType] = $function;
        return $this;
    }

    protected function removeEventListener( $eventType ){
        unset( $this->eventListeners[$eventType] );
        return $this;
    }

    protected function triggerEvent( EventInterface $event ){
        if( isset( $this->eventListeners[$event->getType()] ) ){
            $callable = $this->eventListeners[$event->getType()];
            $callable( $event );
        } elseif( $this->getOwner() ) {
            call_user_func( array( $this->getOwner(), "triggerEvent" ), $event );
        }

        throw new ViewModelException("No listeners found for {$event->getType()} event type");
    }

}
