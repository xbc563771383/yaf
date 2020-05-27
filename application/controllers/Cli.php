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
        $fileName = $this->getParam('file_name');
        if($fileName) {
            ParseLog::countMessageLookNum($fileName);
        } else {
            $logDir =LOG_PATH.'/lookNum/';
            $timeLine = bcsub(time(), 60, 0);

            try {
                if (false != ($handle = opendir($logDir))) {
                    while (false !== ($file = readdir($handle))) {
                        //去掉"“.”、“..”以及带“.xxx”后缀的文件
                        if ($file != "." && $file != "..") {
                            $fileNameArr = explode('_', $file);
                            if(is_array($fileNameArr) && isset($fileNameArr[0]) && isset($fileNameArr[1]) && isset($fileNameArr[2]) && isset($fileNameArr[3]) && isset($fileNameArr[4]) ) {
                                $str = $fileNameArr[0].'-'.$fileNameArr[1].'-'.$fileNameArr[2].' '.$fileNameArr[3].':'.$fileNameArr[4];
                                $fileTimeLine = strtotime($str);
                                if($fileTimeLine < $timeLine) {
                                    ParseLog::countMessageLookNum($file);
                                }
                            }
                        }
                    }
                    //关闭句柄
                    closedir($handle);
                }
            } catch (\Exception $e) {
                // 异常
            }
        }

        return true;
    }
}
