<?php
/**
 * User: Leo
 * Date: 2018-11-11
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$CLASSPATHS = require_once('../config/config_autoload.php');

require_once('../class/autoload.php');

require_once('../config/config.php');

$DBARR = require_once('../config/config_pdo.php');
$CLASSDB = new PdoConnection($DBARR['dsn'], $DBARR['usr'], $DBARR['pwd']);
$CLASSDB->connect();
//print_r($CLASSDB->fetchAll('SELECT NOW()'));exit;
//print_r($CLASSDB->fetchAll('SELECT * FROM `tb_offer`'));exit;

$SM = new ServiceManager($CLASSDB);

$ROUTER_CONFIG = require_once('../config/config_router.php');

$ROUTER = Router::getRouterInstance($ROUTER_CONFIG);
$routeResult = $ROUTER->getRouteResult();
//print_r($routeResult);

$moduleName = $routeResult['module'];
$controllerName = $routeResult['controller'].'Controller';
$actionName = $routeResult['action'].'Action';

if ( class_exists($controllerName) ) {
	$controller = new $controllerName($SM, $ROUTER);
	if ( $controller && $controller instanceof BaseController ) {
		$controller->init();
		if ( method_exists($controller, $actionName) ) {
			$controller->$actionName();
		} else {
			echo "Action $actionName not exists!";
		}
		$controller->display();
	}
} else {
	echo "Controller $controllerName not exists!";
}

$CLASSDB->close();
