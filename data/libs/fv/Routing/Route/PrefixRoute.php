<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:36
 */

namespace fv\Routing\Route;

use fv\Http\Request;
use fv\Http\Response;
use fv\Application\ApplicationLoader;

use fv\Routing\Exception\RoutingException;

class PrefixRoute extends AbstractRoute {

    protected $application;
    protected $prefix;

    function __construct( $params = array() ) {
        if( isset( $params['application'] ) )
            $this->setApplication( $params['application'] );

        if( isset( $params['prefix'] ) )
            $this->setPrefix( $params['prefix'] );
    }

    /**
     * @param \fv\Http\Request $request
     *
     * @return bool|\fv\Http\Response
     * @throws \fv\Routing\Exception\RoutingException
     */
    public function handle( Request $request ){
        $delimiter = "|";
        $statement = $delimiter . "^" . preg_quote( $this->getPrefix(), $delimiter ) . $delimiter . "U";

        if( preg_match( $statement, $request->getUri() ) > 0 ){
            $newUri = preg_replace( $statement, '', $request->getUri() );
            $newUri = '/' . ltrim($newUri, '/');
            $request->setUri( $newUri );
            $request->internal->prefix .= $this->getPrefix();

            if( $this->getApplication() ){
                $applicationLoader = new ApplicationLoader;
                $application = $applicationLoader->getApplication( $this->getApplication() );
                return $application->handle( $request );
            }

            throw new RoutingException( "No application parameter provide. What I have to do with this route?" );
        }

        return false;
    }

    final public function setPrefix( $prefix ) {
        $this->prefix = $prefix;
        return $this;
    }

    final public function getPrefix() {
        return $this->prefix;
    }

    final public function setApplication( $application ) {
        $this->application = $application;
        return $this;
    }

    final public function getApplication() {
        return $this->application;
    }

}
