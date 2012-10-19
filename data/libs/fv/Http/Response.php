<?php

namespace fv\Http;

use fv\Http\Params;

/**
 * User: apple
 * Date: 07.09.12
 * Time: 17:37
 *
 * @property Params $headers
 * @property Params $cookies
 */
class Response {

    const HTTP_RESPONSE_STATUS_OK = 200;

    const HTTP_RESPONSE_STATUS_MOVED_PERMANENTLY = 301;
    const HTTP_RESPONSE_STATUS_FOUND = 302;
    const HTTP_RESPONSE_STATUS_NOT_MODIFIED = 304;

    const HTTP_RESPONSE_STATUS_BAD_REQUEST = 400;
    const HTTP_RESPONSE_STATUS_UNAUTHORIZED = 401;
    const HTTP_RESPONSE_STATUS_PAYMENT_REQUIRED = 402;
    const HTTP_RESPONSE_STATUS_FORBIDDEN = 403;
    const HTTP_RESPONSE_STATUS_PAGE_NOT_FOUND = 404;

    const HTTP_RESPONSE_STATUS_INTERNAL_SERVER_ERROR = 500;
    const HTTP_RESPONSE_STATUS_NOT_IMPLEMENTED = 501;
    const HTTP_RESPONSE_STATUS_BAD_GATEWAY = 502;
    const HTTP_RESPONSE_STATUS_SERVICE_UNAVAILABLE = 503;

    private $status = 200;

    private $body;

    /** @var Params[] */
    private $params = array();

    function __get( $name ){
        return $this->getParams( $name );
    }

    function getParams( $name ){
        if( !isset( $this->params[$name] ) ){
            $this->params[$name] = new Params;
        }

        return $this->params[$name];
    }

    public function setBody( $body ) {
        $this->body = $body;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setStatus( $status ) {
        $this->status = $status;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function send(){
        if( isset( $this->params['cookies'] ) ){
            foreach( $this->cookies as $cookie => $value ){
                setcookie( $cookie, $value );
            }
        }

        if( isset( $this->params['headers'] ) ){
            foreach( $this->headers as $header => $value ){
                header( ucfirst($header). ": " . $value );
            }
        }

        print $this->body;
    }
}
