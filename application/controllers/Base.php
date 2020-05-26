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
     * @param $code
     * @param array $data
     * @return false|string
     */
    public function getJson($code, $data = []) :string {
        $msg = isset(Code::$code[$code]) ? Code::$code[$code] : '未知错误';
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'time' => date('Y-m-d H:i:s')
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $page
     * @param $size
     * @return int
     */
    public function getSkip($page, $size) :int {
        return bcmul(bcsub($page, 1, 0), $size, 0);
    }

    /**
     * @return object
     */
    public function getRedis() :object {
        return \Yaf\Registry::get('redis');
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


    /**
     * @param $file
     * @param $logData
     * @return bool
     */
    public function writeLog($file, $logData) :bool {
        if(!$logData) {
            return false;
        }

        $logFile = APPLICATION_PATH.'/log/'.$file;

        $logData[] = PHP_EOL;
        $logStr = implode("\t", $logData);

        $fp = null;
        $lock = false;

        try {
            $fp = new \SplFileObject($logFile, 'a');
            if(!$fp) {
                // TODO 打开文件失败
            }
        } catch (\Exception $e) {
            // TODO 打开文件异常
        }

        if($fp !== null) {
            try {
                $lock = $fp->flock(LOCK_EX);
                if(!$lock) {
                    // TODO 文件加锁失败
                } else {
                    $lock = true;
                }
            } catch (\Exception $e) {
                // TODO 文件加锁异常
            }
        }

        if(($fp !== null) && $lock) {
            try {
                $write = $fp->fwrite($logStr, strlen($logStr));
                if(!$write) {
                    // TODO 文件写入失败
                }
            } catch (\Exception $e) {
                // TODO 文件写入异常
            }
        }

        if($lock) {
            try {
                $unlock = $fp->flock(LOCK_UN);
                if(!$unlock) {
                    // TODO 释放文件锁失败
                }
            } catch (\Exception $e) {
                // TODO 释放文件锁异常
            }
        }

        if($fp !== null) { // 释放文件资源
            $fp = null;
        }

        return true;
    }

}
