<?php
/**
 * User: cah4a
 * Date: 10.09.12
 * Time: 18:47
 */

namespace fv;

use fv\Routing\Router;
use fv\Application\ApplicationLoader;
use fv\Http\Request;

class Kernel {

    /** @var \fv\Routing\Router */
    protected $router;

    /** @var \fv\Application\ApplicationLoader */
    protected $applicationLoader;

    private $loaded = false;

    function __construct() {
        $this->router = new Router;
        $this->applicationLoader = new ApplicationLoader;
    }

    public function load(){
        $this->router->loadFromConfigFile( 'configs/routes.json' );
        $this->applicationLoader->loadFromConfigFile( 'configs/applications.json' );

        $this->loaded = true;
    }

    public function handle(){
        if( !$this->loaded )
            $this->load();

        $request = Request::buildFromGlobal();
        $request->internal->kernel = $this;

        return $this->getRouter()->handle( $request );
    }

    public function setRouter( $router ) {
        $this->router = $router;
        return $this;
    }

    public function getRouter() {
        return $this->router;
    }
}
