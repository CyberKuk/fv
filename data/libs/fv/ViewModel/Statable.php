<?php
namespace fv\ViewModel;

trait Statable{
    /** @var string */
    private $state;

    /** @var \Bundle\fv\RoutingBundle\Application\AbstractApplication */
    private $context;

    final public function setState( $state ){
        if( !in_array( $state, $this->getStates() ) ){
            throw new \Exception( "Unknown state {$state}. Check 'getStates()' method." );
        }
        $this->state = $state;

        return $this;
    }

    final public function getState(){
        return $this->state;
    }

    final public function setContext( $context ){
        $this->context = $context;

        return $this;
    }

    final public function getContext(){
        return $this->context;
    }

    abstract public function getStates();

    final function getViewModel(){
        if( !$this->getState() ){
            throw new \Exception( "State is not set." );
        }

        $tail = null;
        foreach( $this->context->getNamespacesTree() as $namespace ){
            if( preg_match( "/" . addslashes( $namespace ) . "/", __CLASS__ ) ){
                $tail = preg_replace( "/" . addslashes( $namespace ) . "/", "", __CLASS__ );
            }
        }

        if( $tail === null ){
            throw new \fv\View\Exception\ViewException( "Cannot calculate tail." );
        }

        $className = null;
        foreach( $this->context->getNamespacesTree() as $namespace ){
            $className = sprintf( "%sViewModel\\%s\\%s",
                                  $namespace,
                                  $tail,
                                  $this->state );

            if( class_exists( $className ) ){
                break;
            }
        }

        if( $className === null ){
            throw new \fv\View\Exception\ViewException( "Cannot calculate class name." );
        }

        $viewModel = new $className( $this );

        return $viewModel;
    }

    final function __toString(){
        try{
            return (string)$this->getViewModel();
        }
        catch( \Exception $e ){
            return $e->getMessage();
        }
    }

}
