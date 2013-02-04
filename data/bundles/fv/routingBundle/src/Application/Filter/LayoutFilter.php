<?php

namespace Bundle\fv\RoutingBundle\Application\Filter;

use Bundle\fv\RoutingBundle\Layout\LayoutFactory;

class LayoutFilter extends AbstractFilter {

    /** @var LayoutFactory */
    private $layoutFactory;

    function __construct( $layoutFactory ) {
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Layout\LayoutFactory
     */
    public function getLayoutFactory() {
        return $this->layoutFactory;
    }

    public function execute(FilterChain $chain) {
        $layout = $this->getLayoutFactory()
                ->createLayout( $chain->getRequest() )
                ->setResponse( $chain->execute() );

        $layout->execute();

        return $layout->getResponse();
    }


}
