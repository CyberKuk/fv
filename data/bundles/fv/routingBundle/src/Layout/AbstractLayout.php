<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:16
 */

namespace Bundle\fv\RoutingBundle\Layout;

use fv\Http\Response;
use fv\ViewModel\ViewModel;

abstract class AbstractLayout extends ViewModel {

    /** @var \fv\Http\Response */
    private $response;

    abstract function execute();

    public function __construct(){}

    protected function getLandingPlaces() {
        return array("body");
    }

    public function setBody( ViewModel $body ) {
        $this->land( "body", $body );
        return $this;
    }

    public function getBody() {
        return $this->getLandedOn( "body" );
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
