<?php
chdir('data');

require_once 'libs/classLoader/_init_.php';

/** Direct classes */
ClassLoader\Register::createLoader( null, 'classes' );

/** Libraries */
ClassLoader\Register::createLoader( 'fv', 'libs/fv' );

/** Twig */
require_once 'libs/Twig/Autoloader.php';
Twig_Autoloader::register();