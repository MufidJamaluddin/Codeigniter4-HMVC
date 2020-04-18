<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('land', ['namespace' => 'App\Modules\Land\Controllers'], function($subroutes){

	/*** Route for About ***/
	$subroutes->add('about', 'About::index');

	/*** Route for Home ***/
	$subroutes->add('home', 'Home::index');

});