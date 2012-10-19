<?php
/**
 * User: cah4a
 * Date: 08.10.12
 * Time: 11:34
 */

namespace fv\Routing\Route;

use fv\Http\Request;
use fv\Http\Response;

use fv\Application\AbstractApplication;
use fv\Application\ApplicationLoader;
use fv\Controller\ControllerLoader;

use fv\Routing\Exception\RoutingException;

class DefaultRoute extends AbstractRoute {

    protected $application;
    protected $controller;
    protected $prefix;
    protected $params = array();
    protected $paramValues = array();
    protected $url;

    function __construct( $params = array() ) {
        if( isset( $params['application'] ) )
            $this->setApplication( $params['application'] );

        if( isset( $params['controller'] ) )
            $this->setController( $params['controller'] );

        if( isset( $params['prefix'] ) )
            $this->setPrefix( $params['prefix'] );

        if( isset( $params['params'] ) )
            $this->setParams( $params['params'] );

        if( isset( $params['url'] ) )
            $this->setUrl( $params['url'] );
    }

    /**
     * @param Request $request
     * @return bool
     *
     * @throw \fv\Routing\Exception\RoutingException
     */
    function canHandle( Request $request ) {
        if( $this->getUrl() ){
            return $this->canHandleUrl( $request );
        }

        if( $this->getPrefix() ){
            return $this->canHandlePrefix( $request );
        }

        throw new RoutingException( "You must provide url or prefix param" );
    }

    private function canHandleUrl( Request $request ){
        if( ! $this->getUrl() )
            return false;

        $delimiter = "|";
        $params = array();

        $statement = preg_quote( $this->getUrl(), $delimiter );
        $statement = str_replace( array( "\\{\\$", "\\}" ), array('{$', '}'), $statement );

        $function = function( $matches ) use ( &$params ) {
            $paramName = $matches[1];
            $params[] = $paramName;
            return "(" . ($this->getParam($paramName) ?: '[^/]+') . ")";
        };
        $statement = preg_replace_callback( '|\{\$([\w_]+)\}|', $function, $statement );

        $statement = rtrim($statement, '/');
        $statement = $delimiter . "^" . $statement . "$" . $delimiter . "U";

        $uri = rtrim($request->getUri(), "/");
        if( preg_match( $statement, $uri, $matches ) > 0 ){
            array_shift( $matches );
            $this->setParamValues( array_combine( $params, $matches ) );
            return true;
        }

        return false;
    }

    private function canHandlePrefix( Request $request ){
        $delimiter = "|";
        $statement = $delimiter . "^" . preg_quote( $this->getPrefix(), $delimiter ) . $delimiter . "U";

        if( preg_match( $statement, $request->getUri() ) > 0 ){
            $newUri = preg_replace( $statement, '', $request->getUri() );
            $newUri = '/' . ltrim($newUri, '/');
            $request->setUri( $newUri );
            $request->internal->prefix .= $this->getPrefix();

            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @throw \fv\Routing\Exception\RoutingException
     */
    public function handle( Request $request ) {
        if( $this->getApplication() ){
            $applicationLoader = new ApplicationLoader;
            $application = $applicationLoader->getApplication( $this->getApplication() );
            return $application->handle( $request );
        }

        if( $this->getController() ){
            $controller = $this->getApplicationFromRequest( $request )
                ->getControllerLoader()
                ->createController( $this->getController(), $request );

            call_user_func_array( array( $controller, 'execute' ), $this->getParamValues() );

            return $controller->getResponse();
        }

        throw new RoutingException( "No application or controller param provide. What I have to do with this route?" );
    }

    final public function setApplication( $application ) {
        $this->application = $application;
        return $this;
    }

    final public function getApplication() {
        return $this->application;
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

    final public function setParamValue($name, $value){
        $this->paramValues[$name] = $value;
        return $this;
    }

    final public function setParamValues( array $array ){
        foreach( $array as $name => $value ){
            $this->setParamValue( $name, $value );
        }

        return $this;
    }

    final public function getParamValues(){
        return $this->paramValues;
    }

    final public function setPrefix( $prefix ) {
        $this->prefix = $prefix;
        return $this;
    }

    final public function getPrefix() {
        return $this->prefix;
    }

    final public function setUrl( $url ) {
        $this->url = $url;
        return $this;
    }

    final public function getUrl() {
        return $this->url;
    }
}
