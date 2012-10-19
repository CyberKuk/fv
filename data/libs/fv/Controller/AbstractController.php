<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:27
 */

namespace fv\Controller;

use fv\Viewlet;
use fv\Http\Request;
use fv\Http\Response;
use fv\Application\AbstractApplication;

use \fv\Routing\Exception\RoutingException;

abstract class AbstractController {

    use Viewlet;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var AbstractApplication */
    private $application;

    final function __construct( Request $request ){
        $response = new Response();
        $response->setBody( $this );
        $this->setResponse( $response );
        $this->setRequest( $request );
    }

    /**
     * Calls the class method corresponding HTTP request method.
     * Proxies all the passed parameters to this method
     *
     * If this method does not exist
     * @throws \fv\Routing\Exception\RoutingException
     */
    function execute(){
        $method = strtolower( $this->getRequest()->getMethod() );

        if( !method_exists( $this, $method ) )
            throw new RoutingException( "Controller not implement {$method} method." );

        $result = call_user_func_array( array( $this, $method ), func_get_args() );

        if( is_array($result) ){
            $this->assignParams( $result );
        }
    }

    function getTemplateDir() {
        return $this->getApplication()->getPath() . "views/controller";
    }

    function getTemplateName() {
        $class = str_replace( $this->getApplication()->getControllerNamespace(), "",  get_class($this));
        $class = preg_replace( "/Controller$/", "", $class );
        $class = preg_replace_callback( "/(\\\|^)(\w)/", function( $match ){ return DIRECTORY_SEPARATOR . strtolower($match[2]); }, $class );
        return $class;
    }

    /**
     * @param \fv\Application\AbstractApplication $application
     */
    public function setApplication( AbstractApplication $application ) {
        $this->application = $application;
        return $this;
    }

    /**
     * @return \fv\Application\AbstractApplication
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * @param Request $request
     */
    public function setRequest( Request $request ) {
        $this->request = $request;
        if( $request->internal->application ){
            $this->setApplication( $request->internal->application );
        }
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param Response $response
     */
    public function setResponse( Response $response ) {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }
}
