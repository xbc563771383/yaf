可以按照以下步骤来部署和运行程序:
1.请确保机器root@iz2ze50hce9r0p97uh0nr8z已经安装了Yaf框架, 并且已经加载入PHP;
2.把yaf_skeleton目录Copy到Webserver的DocumentRoot目录下;
3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4.重启Webserver;
5.访问http://yourhost/yaf_skeleton/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;



1. 仅更新单个库
只想更新某个特定的库，不想更新它的所有依赖，很简单：
composer update foo/bar

2. 不编辑composer.json的情况下安装库
你可能会觉得每安装一个库都需要修改composer.json太麻烦，那么你可以直接使用require命令。
composer require "foo/bar:1.0.0"

3. 为生产环境作准备
最后提醒一下，在部署代码到生产环境的时候，别忘了优化一下自动加载：
composer dump-autoload --optimize


下面是yaf的基本使用方式
Yaf_Application::app()->getConfig();
Yaf_Registry::set("config", $config);
$dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
//通过派遣器得到默认的路由器
$router = Yaf_Dispatcher::getInstance()->getRouter();
$router->addRoute('myRoute', $route);
$router->addRoute('myRoute1',$route);
$this->getRequest()->isXmlHttpRequest();
/* 关闭自动响应, 交给rd自己输出*/
$response =$application->getDispatcher()->returnResponse(TRUE)->getApplication()->run();
/** 输出响应*/
$response->response();
设置错误处理函数, 一般在appcation.throwException关闭的情况下, Yaf会在出错的时候触发错误, 这个时候, 如果设置了错误处理函数, 则会把控制交给错误处理函数处理.
Yaf_Dispatcher::getInstance()->getRequest();
Yaf_Dispatcher::throwException切换在Yaf出错的时候抛出异常, 还是触发错误.当然,也可以在配置文件中使用ap.dispatcher.thorwException=$switch达到同样的效果, 默认的是开启状态.
$config = new Yaf_Config_Ini('/path/to/config.ini', 'staging');
echo $config->database->get("params")->host;   // 输出 "dev.example.com"
echo $config->get("database")->params->dbname; // 输出 "dbname"
echo $this->getRequest()->getParam("name"); //
$request = $this->getRequest();
$response = $this->getResponse();
https://www.laruence.com/manual/yaf.class.request.html;关于request
$this->getRequest()->isCli();
$this->getResponse()->setBody("Hello World");
$this->getResponse()->response();
https://www.laruence.com/manual/yaf.class.session.html;关于session

事务
use Illuminate\Database\Capsule\Manager as DB;
DB::beginTransaction();
DB::rollback();
DB::commit();
DB::table('user')->first();
$name = $this->getRequest()->getPost('name', '');
$pwd = $this->getRequest()->getPost('pwd', '');

原生sql有注入危险
$list = DB::select("select * from `user` where (`name` = 'xiebaichuan' and `pwd` = '".$pwd."') limit 1");
Xbc@9023' or '1' = '1



orm使用手册
https://learnku.com/docs/laravel-cheatsheet/5.1

cli模式使用
php index.php "cli/countMessageLookNum" "a=100&b=10"
