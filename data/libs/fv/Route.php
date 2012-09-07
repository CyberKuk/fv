<?php

namespace fv;

/**
 * User: cah4a
 * Date: 07.09.12
 * Time: 17:28
 */
abstract class Route {

    /**
     * @abstract
     * @param Request $request
     * @return mixed
     */
    public abstract function handle( Request $request );

}
