<?php

namespace Bundle\fv\RoutingBundle\Application\Filter;

use fv\Http\Request;

final class FilterChain {

    /** @var AbstractFilter[] */
    private $filters;

    /** @var Request */
    private $request;

    public function addFilter( AbstractFilter $filter ) {
        $this->filters[] = $filter;
        return $this;
    }

    public function execute() {
        /** @var $filter AbstractFilter */
        $filter = array_shift( $this->filters );

        if( $filter )
            return $filter->execute( $this );

        return false;
    }

    final public function setRequest( $request ) {
        $this->request = $request;
        return $this;
    }

    final public function getRequest() {
        return $this->request;
    }

}
