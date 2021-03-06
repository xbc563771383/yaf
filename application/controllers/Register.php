<?php

/**
 * @name RegisterController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class RegisterController extends BaseController
{

    /**
     * 用户注册的短信验证码的有效时长
     * @var int
     */
    private $registerCodeKeep = 300;


    /**
     * 注册发送短信验证码
     * @return bool
     */
    public function getRegisterCodeAction() {
        $mobile = $this->getQuery('mobile');
        if(!$mobile || !is_numeric($mobile)) {
            $this->setBody(Help::getJson(1700));
            return true;
        }

        $code = mt_rand(1000, 9999);
        $res = Help::getRedis()->set(RedisDataKey::$REGISTER_CODE.$mobile, $code, ['nx', 'ex' => $this->registerCodeKeep]);
        if(!$res) {
            $this->setBody(Help::getJson(1701));
            return true;
        }

        $user = UserModel::where('mobile', '=', $mobile)->first();
        if($user) {
            $this->setBody(Help::getJson(1702));
            return true;
        }

        // TODO 发送短信验证码 （先发送后写入，防止机器注册）

        $this->setBody(Help::getJson(1704));
        return true;
    }


    /**
     * 注册提交表单
     * @return bool
     */
    public function registerAction() {
        $mobile = $this->getPost('mobile');
        if(!$mobile || !is_numeric($mobile)) {
            $this->setBody(Help::getJson(1800));
            return true;
        }

        $code = $this->getPost('code');
        if(!$code || !is_numeric($code) || $code >= 10000 || $code < 1000) {
            $this->setBody(Help::getJson(1801));
            return true;
        }

        $headImage = $this->getPost('head_image');
        if(!$headImage) {
            $this->setBody(Help::getJson(1802));
            return true;
        }

        $nickname = $this->getPost('nickname');
        if(!$nickname) {
            $this->setBody(Help::getJson(1803));
            return true;
        }
        if(mb_strlen($nickname) > 255) {
            $this->setBody(Help::getJson(1804));
            return true;
        }

        $des = $this->getPost('des');
        if(!$des || mb_strlen($des) > 255) {
            $this->setBody(Help::getJson(1810));
            return true;
        }

        $password0 = $this->getPost('password_0');
        $password1 = $this->getPost('password_1');
        if(!$password0 || !$password1) {
            $this->setBody(Help::getJson(1811));
            return true;
        }

        if($password1 != $password0) {
            $this->setBody(Help::getJson(1812));
            return true;
        }

        $local = $this->getPost('local');

        $redisCode =  Help::getRedis()->get(RedisDataKey::$REGISTER_CODE.$mobile);
        Help::getRedis()->del(RedisDataKey::$REGISTER_CODE.$mobile);
        if($code != $redisCode) {
            $this->setBody(Help::getJson(1813));
            return true;
        }

        $dateTime = Help::getDateTime();
        $insertData = [
            'mobile' => $mobile,
            'head_image' => $headImage,
            'nickname' => $nickname,
            'des' => $des,
            'password' => $password0,
            'local' => $local,
            'create_at' => $dateTime,
            'update_at' => $dateTime,
        ];

        $userId = UserModel::insertGetId($insertData);
        if(!$userId) {
            $this->setBody(Help::getJson(1814));
            return true;
        }

        unset($insertData['password']); // 避免删除密码
        $this->setBody(Help::getJson(1815, $insertData));
        return true;
    }



}
