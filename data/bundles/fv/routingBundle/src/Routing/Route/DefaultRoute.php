<?php
/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 11:34
 */

namespace Bundle\fv\RoutingBundle\Routing\Route;

use fv\Http\Request;
use fv\Collection\Collection;
use Bundle\fv\RoutingBundle\Application\AbstractApplication;

use Bundle\fv\RoutingBundle\Routing\Exception\RoutingException;

class DefaultRoute extends AbstractRoute {

    protected $controller;
    protected $params = array();
    protected $url;

    function __construct( Collection $params = null ) {
        if( $params->controller )
            $this->setController( $params->controller->get() );

        if( $params->prefix )
            $this->setParams( $params->prefix->get() );

        if( $params->url )
            $this->setUrl( $params->url->get() );
    }

    /**
     * @param Request $request
     *
     * @throws \Bundle\fv\RoutingBundle\Routing\Exception\RoutingException
     * @return bool
     *
     * @throw \Bundle\fv\RoutingBundle\Routing\Exception\RoutingException
     */
    function handle( Request $request ) {
        $uri = rtrim($request->getUri(), "/");

        $this->getPregParams();

        if( preg_match( $this->getPregStatement(), $uri, $matches ) > 0 ){
            array_shift( $matches );
            $values = array_combine( $this->getPregParams(), $matches );

            if( $this->getController() ){
                $application = $request->internal->application;

                if( ! $application instanceof AbstractApplication )
                    throw new RoutingException( "No application to show controller. What I have to do with this route?" );

                $controller = $application
                    ->getControllerFactory()
                    ->createController( $this->getController() );

                $controller->setRequest($request);

                call_user_func_array( array( $controller, 'execute' ), $values );

                $layout = $application->getLayoutFactory()->createLayout( $request );
                $layout
                    ->setResponse( $controller->getResponse() )
                    ->execute();

                return $layout->getResponse();
            }

            throw new RoutingException( "No controller parameter provide. What I have to do with this route?" );
        }

        return false;
    }

    private function getPregStatement(){
        $delimiter = "|";
        $params = array();

        $statement = preg_quote( $this->getUrl(), $delimiter );
        $statement = str_replace( array( "\\{\\$", "\\}" ), array('{$', '}'), $statement );

        $function = function( $matches ) use ( &$params ) {
            $paramName = $matches[1];
            return "(" . ($this->getParam($paramName) ?: '[^/]+') . ")";
        };
        $statement = preg_replace_callback( '|\{\$([\w_]+)\}|', $function, $statement );

        $statement = rtrim($statement, '/');
        $statement = $delimiter . "^" . $statement . "$" . $delimiter . "U";

        return $statement;
    }

    private function getPregParams(){
        preg_match_all( '|\{\$([\w_]+)\}|', $this->getUrl(), $matches );
        return $matches[1];
    }

    final public function setController( $controller ) {
        $this->controller = $controller;
        return $this;
    }

    final public function getController() {
        return $this->controller;
    }

    final public function setParams( $params ) {
        $this->params = $params;
        return $this;
    }

    final public function addParam( $param ) {
        $this->params[] = $param;
        return $this;
    }

    final public function getParams() {
        return $this->params;
    }

    final public function getParam($name){
        if( isset($this->params[$name]) )
            return $this->params[$name];

        return null;
    }

    final public function setUrl( $url ) {
        $this->url = $url;
        return $this;
    }

    final public function getUrl() {
        return $this->url;
    }
}
