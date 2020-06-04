<?php

// Definiramo globalno vidljive constante:
// __SITE_PATH = putanja na disku servera do index.php
// __SITE_URL  = URL do index.php
define( '__SITE_PATH', realpath( dirname( __FILE__ ) ) );
define( '__SITE_URL', dirname( $_SERVER['PHP_SELF'] ) );

//da e možemo vratit natrag 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);

// Započnemo/nastavimo session
session_start();

// Inicijaliziraj aplikaciju (učitava bazne klase, autoload klasa iz modela).
require_once 'app/init.php';
//require_once 'app/boot/prepareDB.php';

// Stvori zajednički registry podataka u aplikaciji.
$registry = new Registry();

// Stvori novi router, spremi ga u registry.
$registry->router = new Router($registry);

// Javi routeru putanju gdje su spremljeni svi controlleri.
$registry->router->setPath( __SITE_PATH . '/controller' );

// Stvori novi template za prikaz view-a.
$registry->template = new Template($registry);

// Učitaj controller pomoću routera.
$registry->router->loader();

?>
