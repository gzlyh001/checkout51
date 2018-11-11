<?php

return array(

	// Home
	'/home.html'
		=> array( 'module' => 'home', 'controller' => 'Index', 'action' => 'index', 'params' => array() ),
	'/index.html'
		=> array( 'module' => 'home', 'controller' => 'Index', 'action' => 'index', 'params' => array() ),

	// API
	'/api/import.html'
		=> array( 'module' => 'home', 'controller' => 'Api', 'action' => 'import', 'params' => array() ),
	'/api/getOffers.html'
		=> array( 'module' => 'home', 'controller' => 'Api', 'action' => 'getOffers', 'params' => array() ),

	// Global Router
	'/^\/([a-zA-Z0-p\-]+)\/([a-zA-Z0-p\-]+)\.html$/'
		=> array( 'module' => 'home', 'controller' => '$1', 'action' => '$2', 'params' => array() ),

	// Default

);
