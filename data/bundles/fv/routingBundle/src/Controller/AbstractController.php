<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:27
 */

namespace Bundle\fv\RoutingBundle\Controller;

use fv\Http\Request;
use fv\Http\Response;

use \Bundle\fv\RoutingBundle\Routing\Exception\RoutingException;

abstract class AbstractController extends \fv\ViewModel\ViewModel {

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    public function __construct(){}

    /**
     * Calls the class method corresponding HTTP request method.
     * Proxies all the passed parameters to this method
     *
     * If this method does not exist
     * @throws \Bundle\fv\RoutingBundle\Routing\Exception\RoutingException
     */
    final public function execute(){
        $method = strtolower( $this->getRequest()->getMethod() );

        if( !method_exists( $this, $method ) )
            throw new RoutingException( "Controller not implement {$method} method." );

        $result = call_user_func_array( array( $this, $method ), func_get_args() );

        if( is_array($result) ){
            $this->assignParams( $result );
        }
    }

    protected function getTemplateClass() {
        return preg_replace( "/Controller$/", "", get_class($this) );
    }

    /**
     * @param Request $request
     * @return \Bundle\fv\RoutingBundle\Controller\AbstractController
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
     * @return Response
     */
    final public function getResponse() {
        if( empty($this->response) ){
            $this->response = new Response();
            $this->response->setBody( $this );
        }
        return $this->response;
    }

    /**
     * @return null|\Bundle\fv\RoutingBundle\Application\AbstractApplication
     */
    public function getApplication(){
        if( $app = $this->getRequest()->internal->application )
            return $app->get();

        return null;
    }

    public function getTemplateName(){}
}
