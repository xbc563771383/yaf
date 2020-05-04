<?php
error_reporting(E_ALL);
/* 定义这个常量是为了在application.ini中引用*/
define('APPLICATION_PATH', dirname(dirname(__FILE__)));

$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
