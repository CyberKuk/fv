<?php
chdir('data');

require_once 'libs/classLoader/_init_.php';

/* Direct classes */
ClassLoader\Register::createLoader( null, 'classes' );

/* Libraries */
ClassLoader\Register::createLoader( 'fv', 'libs/fv' );

/* Bundles */
fv\Bundle\BundleRegister::register("Bundle\\fv\\RoutingBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\ModelBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\SiteEntityBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\MetroUIBundle");

/* Project config */
\fv\Config\ConfigRegister::registerNamespace( "", "configs" );

/* Twig */
require_once 'libs/Twig/Autoloader.php';
Twig_Autoloader::register();