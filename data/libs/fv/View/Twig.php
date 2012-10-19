<?php

/**
 * User: cah4a
 * Date: 13.10.12
 * Time: 18:32
 */

namespace fv\View;

class Twig extends AbstractView {

    private function getTwig(){
        static $twig;


        if( ! isset( $twig ) ){
            $config = \fv\Config\ConfigLoader::load("./configs/twig.json");

            if( !isset( $config['cache'] ) ){
                if( is_dir('./cache') )
                    $config['cache'] =  './cache/twig';
            }

            $loader = new \Twig_Loader_Filesystem('.');
            $twig = new \Twig_Environment($loader, $config);
            //$twig = new \Twig_Environment( $loader );
        }

        return $twig;
    }

    public function setTemplate( $templatePath ) {
        if( preg_match('/\.twig$/', $templatePath) == 0 ){
            return parent::setTemplate( $templatePath . ".twig" );
        }

        return parent::setTemplate( $templatePath );
    }

    function render() {
        return $this->getTwig()->render( $this->getTemplate(), $this->getAssignedParams() );
    }
}
