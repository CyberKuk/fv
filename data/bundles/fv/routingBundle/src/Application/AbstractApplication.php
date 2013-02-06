<?php
/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 18:30
 */

namespace Bundle\fv\RoutingBundle\Application;

use fv\Http\Request;
use Bundle\fv\RoutingBundle\Application\Filter\LayoutFilter;
use fv\Config\ConfigLoader;
use fv\Collection\Collection;

use Bundle\fv\RoutingBundle\Routing\Router;
use Bundle\fv\RoutingBundle\Application\Filter\RouterFilter;
use Bundle\fv\RoutingBundle\Application\Filter\FilterChain;
use Bundle\fv\RoutingBundle\Controller\ControllerFactory;
use Bundle\fv\RoutingBundle\Layout\LayoutFactory;

abstract class AbstractApplication {

    private $filterChain;
    private $path;
    private $namespace;
    private $loaded = false;
    private $router;

    private $namespacesTree;

    public function __construct( Collection $config ) {
        $this->filterChain = new FilterChain();
        $this->path = rtrim( $config->path->get(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->namespace = rtrim( $config->namespace->get(), "\\") . "\\";
    }

    final public function load(){
        if( ! $this->loaded ){
            $this->loaded = true;
            $this->reload();
        }

        return $this;
    }

    protected function reload(){
        $this->router = Router::buildFromCollection( ConfigLoader::load( 'routes', $this ) );
        $this->getFilterChain()->appendFilter( new RouterFilter( $this->router ) );
        $this->getFilterChain()->prependFilter( new LayoutFilter( $this->getLayoutFactory() ) );
}

    /**
     * @param \fv\Http\Request $request
     * @return \fv\Http\Response
     */
    final public function handle( Request $request ){
        $request->internal->application = $this;
        return $this->load()->getFilterChain()->setRequest( $request )->execute();
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Controller\ControllerFactory
     */
    public function getControllerFactory(){
        return new ControllerFactory( $this->getNamespace() . "Controller\\" );
    }

    /**
     * @return \Bundle\fv\RoutingBundle\Layout\LayoutFactory
     */
    public function getLayoutFactory(){
        return new LayoutFactory( $this->getNamespace() . "Layout\\" );
    }

    /**
     * @return Router
     */
    final public function getRouter() {
        return $this->load()->router;
    }

    final public function getFilterChain() {
        return $this->filterChain;
    }

    final public function getNamespace(){
        return $this->namespace;
    }

    final public function getPath(){
        return $this->path;
    }

    final public function getNamespacesTree(){
        if( !$this->namespacesTree ){
            foreach( array_values( class_parents( $this ) ) as $node ){
                foreach( array_keys( \fv\Bundle\BundleRegister::$bundles ) as $bundleNamespace ){
                    if( preg_match( "/" . addslashes( $bundleNamespace ) . "/" , $node ) ){
                        $this->namespacesTree[] = $bundleNamespace. "\\";
                    }
                }
            }
            array_unshift( $this->namespacesTree, $this->getNamespace() );
        }

        return $this->namespacesTree;
    }
}
