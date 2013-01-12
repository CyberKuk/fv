<?php

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

require_once 'data/bootstrap.php';

$kernel = new \fv\Kernel();
$response = $kernel->handle();

$response->send();