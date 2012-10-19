<?php
/**
 * User: cah4a
 * Date: 12.09.12
 * Time: 20:09
 */

namespace classLoader;

include 'Loader.php';
include 'Register.php';

Register::createLoader( 'classLoader', __DIR__ );