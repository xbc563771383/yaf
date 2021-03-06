<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:\Yaf\Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
//use Illuminate\Database\Capsule\Manager as DB;

class Bootstrap extends \Yaf\Bootstrap_Abstract {
    public $config;

    public function _initConfig() {
        //把配置保存起来
        $this->config = \Yaf\Application::app()->getConfig();
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE);
	}

    public function _initError(\Yaf\Dispatcher $dispatcher) {
        if($this->config->application->debug) {
            define('DEBUG_MODE', false);
            ini_set('display_errors', 'On');
        } else {
            define('DEBUG_MODE', false);
            ini_set('display_errors', 'Off');
        }
    }

    public function _initPlugin(\Yaf\Dispatcher $dispatcher) {
		//注册一个插件
		$objSamplePlugin = new SamplePlugin();
		$dispatcher->registerPlugin($objSamplePlugin);
	}

	public function _initRoute(\Yaf\Dispatcher $dispatcher) {
		//在这里注册自己的路由协议,默认使用简单路由
	}

    public function _initAutoload(\Yaf\Dispatcher $dispatcher) {
        // Autoload 自动载入
        Yaf\Loader::import(APPLICATION_PATH.'/vendor/autoload.php');
    }

    public function _initDefaultName(\Yaf\Dispatcher $dispatcher) {
        // 设置默认的接口
        $dispatcher->setDefaultModule('Index')->setDefaultController('Index')->setDefaultAction('index');
    }

    public function _initDatabase(\Yaf\Dispatcher $dispatcher) {
        // Eloquent ORM
        $databaseConfig = $this->config->database->toArray();
        $databaseConfig['options'] = [\PDO::ATTR_EMULATE_PREPARES => true];
        $capsule = new Capsule;
//        $capsule->addConnection($this->config->database->toArray());
        $capsule->addConnection($databaseConfig);
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }


}
