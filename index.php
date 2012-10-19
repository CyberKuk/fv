<?php

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

require_once 'data/bootstrap.php';

use fv\Kernel;

$kernel = new Kernel();
$response = $kernel->handle();

$response->send();