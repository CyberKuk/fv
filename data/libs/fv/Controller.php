<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:27
 */

namespace fv;


abstract class Controller extends Route {

    use Component;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    final function handle( Request $request ){
        $this->setRequest( $request );
        $response = new Response;

        $this->prerender();

        $response->setContent( $this );
    }

    abstract function init();

    /**
     * @param \fv\Request $request
     */
    public function setRequest( $request ) {
        $this->request = $request;
        return $this;
    }

    /**
     * @return \fv\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param \fv\Response $response
     */
    public function setResponse( $response ) {
        $this->response = $response;
        return $this;
    }

    /**
     * @return \fv\Response
     */
    public function getResponse() {
        return $this->response;
    }
}
