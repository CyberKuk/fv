<?php
chdir('data');

require_once 'libs/classLoader/_init_.php';

/** Direct classes */
ClassLoader\Register::createLoader( null, 'classes' );

/** Libraries */
ClassLoader\Register::createLoader( 'fv', 'libs/fv' );

/** Bundles */
fv\Bundle\BundleRegister::register("OrmBundle", "bundles/fv/ormBundle");
fv\Bundle\BundleRegister::register("RoutingBundle");

/** Twig */
require_once 'libs/Twig/Autoloader.php';
Twig_Autoloader::register();