<?php
chdir('data');

require_once 'libs/classLoader/_init_.php';

/* Direct classes */
ClassLoader\Register::createLoader( null, 'src' );

/* Libraries */
ClassLoader\Register::createLoader( 'fv', 'libs/fv' );

/* Bundles */
fv\Bundle\BundleRegister::register("Bundle\\fv\\RoutingBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\ModelBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\SiteEntityBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\MetroUIBundle");
fv\Bundle\BundleRegister::register("Bundle\\fv\\SessionBundle");

/* Project config */
fv\Config\ConfigRegister::registerNamespace( "", "configs" );


/* DB configs */
$connectionFactory = new \fv\Connection\ConnectionFactory();
$connectionFactory->loadFromConfigFile( 'connections' );

/* Twig */
require_once 'libs/Twig/Autoloader.php';
Twig_Autoloader::register();