<?php

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

require_once 'data/bootstrap.php';

$kernel = new \RoutingBundle\Kernel();
$response = $kernel->handle();

$response->send();