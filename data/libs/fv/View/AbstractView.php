<?php
/**
 * User: cah4a
 * Date: 13.10.12
 * Time: 18:48
 */

namespace fv\View;

abstract class AbstractView {

    private $template;
    private $params = array();

    public function setTemplate( $templatePath ) {
        $this->template = $templatePath;
        return $this;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function assignParams( array $params ){
        foreach( $params as $key => $value ){
            $this->assignParam( $key, $value );
        }

        return $this;
    }

    public function assignParam( $key, $value ){
        $this->params[$key] = $value;
        return $this;
    }

    public function getAssignedParams(){
        return $this->params;
    }

    abstract function render();

}
