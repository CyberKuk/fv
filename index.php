<?php
error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

require_once 'data/bootstrap.php';

use Bundle\fv\RoutingBundle\Kernel;

$kernel = new Kernel();
$response = $kernel->handle();

$response->send();