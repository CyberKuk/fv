<?php

namespace fv\Config;

interface Configurable {

    /**
     * Sugar solution.
     * And I understand that abstract static methods - not the brightest idea,
     * but this is the only way to ensure that the class containing factory method
     *
     * @param \fv\Collection\Collection $config
     * @return mixed
     */
    static function build( \fv\Collection\Collection $config );

}
