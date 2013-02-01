<?php
/**
 * User: cah4a
 * Date: 22.10.12
 * Time: 12:36
 */

namespace Bundle\fv\RoutingBundle\Routing\Route;

use fv\Http\Request;
use fv\Collection\Collection;
use fv\Http\Response;
use Bundle\fv\RoutingBundle\Application\ApplicationBuilder;

use Bundle\fv\RoutingBundle\Routing\Exception\RoutingException;

class PrefixRoute extends AbstractRoute {

    protected $applicationName;
    protected $prefix;

    public function __construct( Collection $params = null ) {
        $this
            ->setApplicationName( $params->application->get() )
            ->setPrefix( $params->prefix->get() );
    }

    /**
     * @param \fv\Http\Request $request
     *
     * @return bool|\fv\Http\Response
     * @throws \Bundle\fv\RoutingBundle\Routing\Exception\RoutingException
     */
    public function handle( Request $request ){
        $delimiter = "|";
        $statement = $delimiter . "^" . preg_quote( $this->getPrefix(), $delimiter ) . $delimiter . "U";

        if( preg_match( $statement, $request->getUri() ) > 0 ){
            $newUri = preg_replace( $statement, '', $request->getUri() );
            $newUri = '/' . ltrim($newUri, '/');
            $request->setUri( $newUri );
            if( $request->internal->prefix )
                $request->internal->prefix = $request->internal->prefix->get() . $this->getPrefix();
            else
                $request->internal->prefix = $this->getPrefix();

            if( $this->getApplicationName() ){
                $applicationFactory = new ApplicationBuilder;
                $application = $applicationFactory->build( $this->getApplicationName() );
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

    final public function setApplicationName( $applicationName ) {
        $this->applicationName = $applicationName;
        return $this;
    }

    final public function getApplicationName() {
        return $this->applicationName;
    }

    public function createLink( array $params = null ) {
        return $this->getPrefix();
    }


}
