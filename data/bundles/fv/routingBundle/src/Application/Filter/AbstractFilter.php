<?php

namespace Bundle\fv\RoutingBundle\Application\Filter;

abstract class AbstractFilter {

    abstract public function execute( FilterChain $chain );

}
