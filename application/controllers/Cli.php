<?php
/**
 * @name CliController
 * @author root
 * @desc cli控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class CliController extends BaseController {

    public function init() {
        //关闭自动渲染, 由我们手工返回Json响应
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE);
        if(!$this->isCli()) {
            throw new \Exception('非cli模式无法访问');
        }
    }


    /**
     * 默认动作
     * Yaf支持直接把\Yaf\Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/yaf_skeleton/index/index/index/name/root 的时候, 你就会发现不同
     */
    public function countMessageLookNumAction() {
        $dateTime = $this->getParam('date_time');
        if(!$dateTime) {

        }
    }


}
