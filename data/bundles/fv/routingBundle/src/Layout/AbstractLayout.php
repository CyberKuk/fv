<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:16
 */

namespace Bundle\fv\RoutingBundle\Layout;

use fv\Http\Response;
use Bundle\fv\RoutingBundle\Application\AbstractApplication;

abstract class AbstractLayout extends \fv\ViewModel\ViewModel {

    /** @var \fv\Http\Response */
    private $response;

    private $body;

    abstract function execute();

    public function setBody( $body ) {
        $this->body = $body;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    /**
     * @param \fv\Http\Response $response
     *
     * @return AbstractLayout
     */
    final public function setResponse( Response $response ) {
        $this->setBody( $response->getBody() );
        $response->setBody( $this );
        $this->response = $response;

        return $this;
    }

    /**
     * @return \fv\Http\Response
     */
    public function getResponse() {
        return $this->response;
    }

    protected function getTemplateClass() {
        return preg_replace( "/Layout$/", "", get_class($this) );
    }
}
