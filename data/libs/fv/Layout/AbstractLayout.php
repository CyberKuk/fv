<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:16
 */

namespace fv\Layout;

use \fv\Http\Response;
use \fv\Application\AbstractApplication;

use \fv\Viewlet;

abstract class AbstractLayout {

    use Viewlet;

    /** @var \fv\Http\Response */
    private $response;

    /** @var \fv\Application\AbstractApplication */
    private $application;

    private $body;

    abstract function execute();

    function getTemplateDir() {
        return $this->getApplication()->getPath() . "views/layout";
    }

    function getTemplateName() {
        $path = str_replace( $this->getApplication()->getLayoutNamespace(), "",  get_class($this));
        $path = preg_replace( "/Layout$/", "", $path );
        $path = preg_replace_callback( "/(\\\|^)(\w)/", function( $match ){ return DIRECTORY_SEPARATOR . strtolower($match[2]); }, $path );
        return $path;
    }

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

    /**
     * @param \fv\Application\AbstractApplication $application
     */
    public function setApplication( $application ) {
        $this->application = $application;
        return $this;
    }

    /**
     * @return \fv\Application\AbstractApplication
     */
    public function getApplication() {
        return $this->application;
    }

}
