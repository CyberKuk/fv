<?php

namespace fv\Storage;

use fv\Collection\Collection;

interface Storage {

    public function get( $key );

    public function set( $key, $value );

    public static function build( Collection $config );

}
