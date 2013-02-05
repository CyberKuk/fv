<?php

namespace fv\ViewModel;

use fv\ViewModel\Exception\ViewModelException;

class ViewModel {

    use Viewlet {
        Viewlet::prerender as viewletPrerender;
    }

    /** @var ViewModel|null */
    private $owner;

    /** @var callable[] */
    private $eventListeners = array();

    /** @var ViewModel[] */
    private $places = array();

    /**
     * @param ViewModel $owner
     * @return ViewModel
     */
    private function setOwner( ViewModel $owner) {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return ViewModel|null
     */
    final public function getOwner() {
        return $this->owner;
    }

    /**
     * @param string $place
     * @return ViewModel|null
     * @throws Exception\ViewModelException
     */
    final public function getLandedOn( $place ){
        if( ! $this->isLandingPlaceExist($place) ){
            throw new ViewModelException("Landing place {$place} not exist");
        }

        if( ! isset( $this->places[$place] ) )
            return null;

        return $this->places[$place];
    }

    /**
     * @param string $place
     * @param ViewModel $paratrooper
     * @return ViewModel
     * @throws Exception\ViewModelException
     */
    final public function land( $place, ViewModel $paratrooper ){
        if( ! $this->isLandingPlaceExist($place) ){
            throw new ViewModelException("Landing place {$place} not exist");
        }

        $paratrooper->setOwner( $this );
        $this->places[$place] = $paratrooper;

        return $this;
    }

    /**
     * @param string $place
     * @return bool
     */
    final public function isLandingPlaceExist( $place ){
        return in_array( $place, $this->getLandingPlaces() );
    }

    /**
     * @return string[]
     */
    protected function getLandingPlaces(){
        return array();
    }

    final protected function addEventListener( $eventType, Callable $function ){
        $this->eventListeners[$eventType] = $function;
        return $this;
    }

    final protected function removeEventListener( $eventType ){
        unset( $this->eventListeners[$eventType] );
        return $this;
    }

    final protected function triggerEvent( EventInterface $event ){
        if( isset( $this->eventListeners[$event->getType()] ) ){
            $callable = $this->eventListeners[$event->getType()];
            return $callable( $event );
        } elseif( $this->getOwner() ) {
            return $this->getOwner()->triggerEvent( $event );
        }

        throw new ViewModelException("No listeners found for {$event->getType()} event type");
    }

    final public function prerender() {
        foreach( $this->places as $viewModel ){
            $viewModel->prerender();
        }

        return $this->viewletPrerender();
    }


}
