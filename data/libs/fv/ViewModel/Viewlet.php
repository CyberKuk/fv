<?php

namespace fv\ViewModel;

use fv\View\AbstractView;

trait Viewlet {

    private $content;
    private $params = array();

    final public function __toString(){
        try{
            return $this->render();
        }
        catch( \Exception $e ){
            return $e->getMessage();
        }
    }

    final public function render(){
        return $this->prerender()->content;
    }

    protected function prepareRender(){

    }

    final public function prerender(){
        if( isset( $this->content ) )
            return $this;

        $this->prepareRender();
        $this->assignParam( 'this', $this );

        try{
            $this->content = $this->getView()
                ->assignParams( $this->params )
                ->render();
        } catch( \Exception $e ){
            $this->content = $e->getMessage();
        }

        return $this;
    }

    final protected function assignParams( array $params ){
        foreach( $params as $name => $value )
            $this->assignParam( $name, $value );

        return $this;
    }

    final protected function assignParam( $name, $value ){
        if( isset($this->params[$name]) )
            throw new \Exception( "Variable {$name} already assigned!" );

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @return AbstractView
     */
    protected function getView(){
        return \fv\View\ViewBuilder::build( $this );
    }

}
