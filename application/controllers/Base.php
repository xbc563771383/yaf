<?php
/**
 * @name BaseController
 * @author root
 * @desc 基础控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */

class BaseController extends \Yaf\Controller_Abstract {
    public $userInfo = [];

    /**
     * 默认初始化方法，如果不需要，可以删除掉这个方法
     * 如果这个方法被定义，那么在Controller被构造以后，Yaf会调用这个方法
     */
    public function init() {
        //关闭自动渲染, 由我们手工返回Json响应
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE);
    }

    /**
     * @return string
     */
    public function getActionName() :string {
        return $this->getRequest()->getActionName();
    }

    /**
     * @return string
     */
    public function getControllerName() :string {
        return $this->getRequest()->getControllerName();
    }

    /**
     * @return string
     */
    public function getModuleName() :string {
        return $this->getRequest()->getModuleName();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getQuery($key) {
        return $this->getRequest()->getQuery($key);
    }

    /**
     * @return bool
     */
    public function isCli() :bool {
        return $this->getRequest()->isCli();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key) {
        return $this->getRequest()->getParam($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getPost($key) {
        return $this->getRequest()->getPost($key);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function setBody($string) {
        return $this->getResponse()->setBody($string);
    }

    /**
     * @return string
     */
    public function getCachePrefix() :string {
        return 'cache:'.$this->getModuleName().'_'.$this->getControllerName().'_'.$this->getActionName().'_';
    }
}
