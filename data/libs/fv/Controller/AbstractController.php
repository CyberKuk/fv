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

    final function __construct(){
        $response = new Response();
        $response->setBody( $this );
        $this->setResponse( $response );
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
        $path = str_replace( $this->getApplication()->getControllerNamespace(), "",  get_class($this));
        $path = preg_replace( "/Controller$/", "", $path );
        $path = preg_replace_callback( "/(\\\|^)(\w)/", function( $match ){ return DIRECTORY_SEPARATOR . strtolower($match[2]); }, $path );
        return $path;
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
