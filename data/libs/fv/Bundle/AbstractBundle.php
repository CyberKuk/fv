<?php

namespace fv\Bundle;

abstract class AbstractBundle {

    public function __construct(){}

    /**
     * @return array of dependent bundle namespaces
     */
    abstract public function getDependencies();

}
