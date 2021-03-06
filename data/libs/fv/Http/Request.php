<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 17:34
 */

namespace fv\Http;

use fv\Collection;

/**
 * @final
 *
 * @property \fv\Collection $get
 * @property \fv\Collection $post
 * @property \fv\Collection $cookie
 * @property \fv\Collection $internal
 * @property \fv\Collection $header
 */
final class Request {

    const HTTP_REQUEST_METHOD_GET = 'GET';
    const HTTP_REQUEST_METHOD_POST = 'POST';
    const HTTP_REQUEST_METHOD_PUT = 'PUT';
    const HTTP_REQUEST_METHOD_DELETE = 'DELETE';

    protected $method = self::HTTP_REQUEST_METHOD_GET;

    protected $uri = '/';

    protected $headers = array();

    protected $params = array();

    final static function buildFromGlobal(){
        $request = new static;

        foreach( getallheaders() as $key => $value )
            $request->header->$key = $value;

        foreach( $_GET as $key => $value )
            $request->get->$key = $value;

        foreach( $_POST as $key => $value )
            $request->post->$key = $value;

        foreach( $_COOKIE as $key => $value )
            $request->cookie->$key = $value;

        $request
            ->setMethod( $_SERVER['REQUEST_METHOD'] )
            ->setUri( preg_replace( '/\?.*$/U', '', $_SERVER['REQUEST_URI'] ) );

        return $request;
    }

    function __get( $name ){
        return $this->getParams( $name );
    }

    function getParams( $name ){
        if( !isset( $this->params[$name] ) ){
            $this->params[$name] = new Collection;
        }

        return $this->params[$name];
    }

    function isPost(){
        return strtoupper($this->getMethod()) == self::HTTP_REQUEST_METHOD_POST;
    }

    public function setMethod( $requestMethod ) {
        $this->method = strtoupper($requestMethod);
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setUri( $uri ) {
        $this->uri = $uri;
        return $this;
    }

    public function getUri() {
        return $this->uri;
    }

    public function isXmlHttp(){
        return false;
    }

}
