<?php
/**
 * @name BaseController
 * @author root
 * @desc 基础控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class BaseController extends \Yaf\Controller_Abstract {
    /**
     * 默认初始化方法，如果不需要，可以删除掉这个方法
     * 如果这个方法被定义，那么在Controller被构造以后，Yaf会调用这个方法
     */
    public function init() {
        //关闭自动渲染, 由我们手工返回Json响应
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE);
    }
}
