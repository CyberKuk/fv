<?php

require_once 'SplClassLoader.php';

$fvLoader = new SplClassLoader( 'fv', 'libs/fv' );
$fvLoader->register();