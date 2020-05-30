<?php


use Elasticsearch\ClientBuilder;
class Help {
    /**
     * @param $code
     * @param array $data
     * @return false|string
     */
    public static function getJson($code, $data = []) :string {
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
    public static function getSkip($page, $size) :int {
        return bcmul(bcsub($page, 1, 0), $size, 0);
    }


    /**
     * @return object
     */
    public static function getRedis() :object {
        return \Yaf\Registry::get('redis');
    }



    /**
     * @param $path
     * @param $file
     * @param $logData
     * @return bool
     */
    public static function writeLog($path, $file, $logData) :bool {
        if(!$logData) {
            return false;
        }

        $logFile = LOG_PATH.'/'.$path.'/'.$file;

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


    /**
     * es 测
     * @return bool
     */
    public static function test(){
        $hosts = [
            '47.114.180.172:9200',         // IP + Port
        ];
        $client = ClientBuilder::create()->setHosts($hosts)->setRetries(0)->build();

        $params = [
            'index' => 'message',
            'type' => 'message',
            'body' => [
                'query' => [
                    'match' => ['content' => '热'],
                ]
            ]
        ];

        $response = $client->search($params);
        print_r($response);die;
        return true;
    }


    /**
     * @param $day
     * @return int
     */
    public static function getDayTime($day) :int {
        if(!is_numeric($day)) {
            return 0;
        }
        return bcmul(86400, $day, 0);
    }


    /**
     * @param $data
     * @return false|string
     */
    public static function jsonEncode($data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public static function getDateTime($time = '') {
        if(!$time) {
            $time = time();
        }
        return date('Y-m-d H:i:s', $time);
    }
}


