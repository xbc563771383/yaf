<?php
error_reporting(E_ALL);
/* 定义这个常量是为了在application.ini中引用*/
define('APPLICATION_PATH', dirname(dirname(__FILE__)));
define('LOG_PATH', dirname(dirname(__FILE__)).'/log');


$application = new \Yaf\Application(APPLICATION_PATH . "/conf/application.ini");
$application->bootstrap();

if (!isset($argv[1])) {
    exit('Please enter the route to execute. Example: the php cli.php Index/Index!'.PHP_EOL);
}

$routeArr = explode('/', $argv[1]);
if (count($routeArr) != 2) {
    exit('Please enter the route to execute. Example: the php cli.php Index/Index!'.PHP_EOL);
}

$controllerName = $routeArr[0];
$actionName = $routeArr[1];

$params = [];
if (isset($argv[2])) {
    parse_str($argv[2], $params);
}

$request = new \Yaf\Request\Simple('CLI', 'Index', $controllerName, $actionName, $params);
$application->getDispatcher()->dispatch($request);