<?php
/**
 * User: Leo
 * Date: 2018-04-01
 */

/*
* config_router.php

return array(
	'/home.html'
		=> array( 'module' => 'home', 'controller' => 'index', 'action' => 'index', 'params' => array() ),
	'/^\/product\.html$/'
		=> array( 'module' => 'product', 'controller' => 'index', 'action' => 'list', 'params' => array() ),
	'/^\/(\d+)\/product\.html$/'
		=> array( 'module' => 'product', 'controller' => 'index', 'action' => 'show', 'params' => array( 'id' => '$1' ) ),
	'/^\/([a-zA-Z0-p\-]+)\/product\.html$/'
		=> array( 'module' => 'product', 'controller' => 'index', 'action' => 'show', 'params' => array( 'name' => '$1' ) ),
	'/^\/([a-zA-Z0-p\-]+)\/([a-zA-Z0-p\-]+)\.html$/'
		=> array( 'module' => 'product', 'controller' => '$1', 'action' => '$2', 'params' => array() ),
);

*/

Class Router {
	
	private static $router = null;
	
	private $config = array();
	private $module = 'home';
	private $controller = 'index';
	private $action = 'index';
	private $params = array();
	
	/**
	 * __construct
	 * @param array $config
	 * @return void
	 */
	public function __construct ($config) {
		$this->config = $config;
	}
	
	/**
	 * setModule
	 * @param string $module
	 * @return void
	 */
	public function setModule($module) {
		$this->module = $module;
	}
	
	/**
	 * getModule
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * setController
	 * @param string $controller
	 * @return void
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}
	
	/**
	 * getController
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}
	
	/**
	 * setAction
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * getAction
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * setParams
	 * @param array $params
	 * @return void
	 */
	public function setParams($params) {
		$this->params = $params;
	}
	
	/**
	 * getParams
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * getParameter
	 * @param string $param
	 * @return mixed
	 */
	public function getParameter($param) {
		if ( isset($this->params[$param]) ) {
			return $this->params[$param];
		}
		return null;
	}
	
	/**
	 * getRouteResult
	 * @return array
	 */
	public function getRouteResult() {
		return array(
			'module'		=>	$this->getModule(),
			'controller'	=>	$this->getController(),
			'action'		=>	$this->getAction(),
			'params'		=>	$this->getParams(),
		);
	}
	
	/**
	 * extractConfig
	 * @param array $conf
	 * @param array $variabls
	 * @return array
	 */
	public static function extractConfig($conf, $variabls) {
		$extResult = array();
		foreach ( $conf as $key => $val ) {
			if ( is_array($val) ) {
				$extResult[$key] = self::extractConfig($val, $variabls);
			} else {
				if ( preg_match('/^\$(\d+)$/', $val, $matches) && isset($variabls[$matches[1]] ) ) {
					$val = $variabls[$matches[1]];
				}
				$extResult[$key] = $val;
			}
		}
		return $extResult;
	}
	
	/**
	 * getDefaultRoute
	 * @return array
	 */
	public function getDefaultRoute() {
		if ( isset($this->config['DEFAULT']) ) {
			return $this->config['DEFAULT'];
		} else {
			return array(
				'module' => 'home', 'controller' => 'Index', 'action' => 'index', 'params' => array()
			);
		}
	}

	/**
	 * parseUrl
	 * @param string $url
	 * @return mixed
	 */
	public function parseUrl($url) {
		
		$matchedType = 0;
		$matchedPattern = false;
		$matchedConfig = false;
		$matchedExtract = array();
		
		foreach ( $this->config as $pattern => $routerConfig ) {
			if ( $pattern === $url ) {
				$matchedType = 1;
				$matchedPattern = $pattern;
				$matchedConfig = $routerConfig;
			} else {
				if ( substr($pattern, 0 , 1) === '/' &&
					( substr($pattern, -1) === '/' || substr($pattern, -2, 2) === '/i' ) &&
					preg_match($pattern, $url, $matches) ) {
					$matchedType = 2;
					$matchedPattern = $pattern;
					$matchedConfig = $routerConfig;
					$matchedExtract = $matches;
				}
			}
			if ( $matchedType ) { break; }
		}
		
		if ( ! $matchedType ) { return false; }
		
		$parseResults = $matchedConfig;
		if ( $matchedType == 2 ) {
			$parseResults = self::extractConfig($matchedConfig, $matchedExtract);
		}
		
		return $parseResults;
	}

	/**
	 * parseUrl
	 * @param string $url
	 * @param array $query
	 * @param array $post
	 * @return void
	 */
	public function routeUrl($url, $query, $post) {
		
		$parseResults = $this->parseUrl($url);
		if ( ! $parseResults ) {
			$parseResults = $this->getDefaultRoute();
		}
		if ( $query ) {
			foreach ( $query as $key => $val ) {
				if ( is_array($val) ) {
					$parseResults['params'][$key] = $val;
				} else {
					$parseResults['params'][$key] = urldecode($val);
				}
			}
		}
		if ( $post ) {
			foreach ( $post as $key => $val ) {
				$parseResults['params'][$key] = $val;
			}
		}
		
		if ( isset($parseResults['module']) ) {
			$this->setModule($parseResults['module']);
		}
		if ( isset($parseResults['controller']) ) {
			$this->setController($parseResults['controller']);
		}
		if ( isset($parseResults['action']) ) {
			$this->setAction($parseResults['action']);
		}
		if ( isset($parseResults['params']) ) {
			$params = $parseResults['params'];
			$this->setParams($parseResults['params']);
		}
		
	}
	
	/**
	 * getRouterInstance
	 * @param array $config
	 * @return Router
	 */
	public static function getRouterInstance($config) {
		
		if ( self::$router === null ) {
			
			$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
			if ( ! $url ) { $url = '/'; }
			
			//$url = '/home.html';
			//$url = '/product.html';
			//$url = '/123/product.html';
			//$url = '/abc/product.html';
			//$url = '/liu/leo.html';
			if ( isset($_GET['url_for_test']) ) {
				$url = urldecode($_GET['url_for_test']);
			}
			
			$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
			if ( $query ) {	parse_str($query, $query); }
			
			$router = new Router($config);
			$router->routeUrl($url, $query, $_POST);

			self::$router = $router;
		}
		
		return self::$router;
	}

}
