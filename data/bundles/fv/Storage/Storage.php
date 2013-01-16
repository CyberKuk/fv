<?php

namespace Bundle\fv\Storage;

interface Storage {

    public function get( $key );

    public function set( $key, $value );

    public static function build( \fv\Collection $config );

}
