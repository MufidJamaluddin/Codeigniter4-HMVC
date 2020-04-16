<?php

$routes->group('admin', ['namespace' => 'App\Modules\Admin\Controllers'], function($subroutes){

	/*** Route for Dashboard ***/
	$subroutes->add('dashboard', 'Dashboard::index');

});