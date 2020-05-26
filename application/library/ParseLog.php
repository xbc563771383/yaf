<?php

use Illuminate\Database\Capsule\Manager as DB;

class ParseLog {
    /**
     * @param string $file
     * @return bool
     */
    public static function countMessageLookNum($file = '') :bool {
        $logFile = APPLICATION_PATH.'/log/lookNum/'.$file;

        if(!file_exists($logFile)) {
            return true;
        }

        $fp = null; // 文件句柄
        $lock = false; // 文件锁定标识

        try {
            $fp = new \SplFileObject($logFile, 'r');
            if(!$fp) {
                // TODO 打开文件失败
            }
        } catch (\Exception $e) {
            // TODO 打开文件异常
        }

        if($fp !== null) {
            try {
                $lock = $fp->flock(LOCK_EX | LOCK_NB);
                if(!$lock) {
                    // TODO 文件加锁失败
                } else {
                    $lock = true;
                }
            } catch (\Exception $e) {
                // TODO 文件加锁异常
            }
        }

        // 打开成功同时拿到文件锁，处理文件
        if(($fp !== null) && $lock) {
            $updateData = [];
            try {
                $fp->seek(0);
                while (!$fp->eof()) {
                    $str = $fp->current();
                    if($str) {
                        $arr = explode("\t", $str);
                        if(is_array($arr) && isset($arr[0]) && $arr[0]) {
                            if(isset($updateData[$arr[0]])) {
                                $updateData[$arr[0]] = $updateData[$arr[0]]+1;
                            } else {
                                $updateData[$arr[0]] = 1;
                            }
                        }
                        $fp->next();// 下一行
                    }
                }
            } catch (\Exception $e) {
                // TODO 文件读取异常
            }

            try {
                foreach ($updateData as $k => $v) {
                    DB::beginTransaction();
                    $messageInfo = DB::table('message')->lockForUpdate()->find($k);
                    if($messageInfo){
                        $updateRes = DB::table('message')->where('id', '=', $k)->update(['look_num' => bcadd($messageInfo->look_num, $v, 0)]);
                        if($updateRes) {
                            DB::commit();
                        } else {
                            DB::rollBack();
                        }
                    } else {
                        DB::rollBack();
                    }
                }

            } catch (\Exception $e) {
                DB::rollBack();
                // TODO 批量更新数据库异常
            }
        }

        if($lock) {
            try {
                $unlock = $fp->flock(LOCK_UN);
                if(!$unlock) {
                    // TODO 文件解锁失败
                }
            } catch (\Exception $e) {
                // TODO 文件解锁异常
            }
        }

        if($fp !== null) {
            $fp = null;
        }
        unlink($logFile);

        return true;
    }

}