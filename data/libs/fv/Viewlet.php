<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:36
 */

namespace fv;

use fv\View\Twig as ViewEngine;

trait Viewlet {

    private $content;
    private $params = array();

    public function __toString(){
        return $this->render();
    }

    public function render(){
        return $this->prerender()->content;
    }

    public function prerender(){
        if( isset( $this->content ) )
            return $this;

        $this->assignParam( 'this', $this );

        try{
            $this->content = $this->getView()
                ->setTemplate( $this->getTemplateDir() . $this->getTemplateName() )
                ->assignParams( $this->params )
                ->render();
        } catch( \Exception $e ){
            $this->content = $e->getMessage();
        }

        return $this;
    }

    protected function assignParams( array $params ){
        foreach( $params as $name => $value )
            $this->assignParam( $name, $value );

        return $this;
    }

    protected function assignParam( $name, $value ){
        if( isset($this->params[$name]) )
            throw new \Exception( "Variable {$name} already assigned!" );

        $this->params[$name] = $value;

        return $this;
    }

    protected function getView(){
        return new ViewEngine;
    }

    abstract function getTemplateName();

    abstract function getTemplateDir();
}