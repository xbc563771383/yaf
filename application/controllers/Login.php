<?php

/**
 * @name LoginController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class LoginController extends BaseController
{


    /**
     * 登录成功后用户数据缓存持续时长
     * @var int
     */
    private $cacheUserInfoKeepDay = 1;


    /**
     * 密码登陆
     * @return bool
     */
    public function passwordLoginAction() {
        $mobile = $this->getQuery('mobile');
        if(!$mobile || !is_numeric($mobile)) {
            $this->setBody(Help::getJson(1400));
            return true;
        }

        $password = $this->getQuery('password');
        if(!$password || !is_string($password)) {
            $this->setBody(Help::getJson(1401));
            return true;
        }

        $user = UserModel::where('mobile', '=', $mobile)->first();
        if(!$user) {
            $this->setBody(Help::getJson(1402));
            return true;
        }

        if($user->password != $password) {
            $this->setBody(Help::getJson(1403));
            return true;
        }

        $user->cache_at = date('Y-m-d H:i:s');
        Help::getRedis(false)->set(RedisCacheKey::$USER_INFO.$user->id, Help::jsonEncode($user), Help::getDayTime($this->cacheUserInfoKeepDay));
        $this->setBody($json = Help::getJson(1404, $user));
        return true;
    }


    /**
     * 手机验证码的持续时长
     * @var int
     */
    private $resetPasswordCodeKeep = 60;


    /**
     * 重置密码发送手机验证码
     * @return bool
     */
    public function getResetPasswordCodeAction() {
        $mobile = $this->getQuery('mobile');
        if(!$mobile || !is_numeric($mobile)) {
            $this->setBody(Help::getJson(1500));
            return true;
        }

        $code = Help::getRedis()->set(RedisDataKey::$RESET_PASSWORD_CODE.$mobile);
        if($code) {
            $this->setBody(Help::getJson(1501));
            return true;
        }

        $user = UserModel::where('mobile', '=', $mobile)->first();
        if(!$user) {
            $this->setBody(Help::getJson(1502));
            return true;
        }

        // TODO 发送短信验证码 （先发送后写入，防止机器注册）

        $code = mt_rand(1000, 9999);
        $res = Help::getRedis()->set(RedisDataKey::$RESET_PASSWORD_CODE.$mobile, $code, $this->resetPasswordCodeKeep);
        if(!$res) {
            $this->setBody(Help::getJson(1504));
            return true;
        }

        $this->setBody(Help::getJson(1505));
        return true;
    }


    /**
     * 手机号重置密码
     * @return bool
     */
    public function resetPasswordAction() {
        $mobile = $this->getPost('mobile');
        if(!$mobile || !is_numeric($mobile)) {
            $this->setBody(Help::getJson(1600));
            return true;
        }

        $code = $this->getPost('code');
        if(!$code || !is_numeric($code) || $code >= 10000) {
            $this->setBody(Help::getJson(1601));
            return true;
        }

        $password0 = $this->getPost('password_0');
        $password1 = $this->getPost('password_1');
        if(!$password0 || !$password1) {
            $this->setBody(Help::getJson(1602));
            return true;
        }

        if($password1 != $password0) {
            $this->setBody(Help::getJson(1603));
            return true;
        }

        $redisCode = Help::getRedis()->get(RedisDataKey::$RESET_PASSWORD_CODE.$mobile);
        if(!$redisCode || $redisCode != $code) {
            $this->setBody(Help::getJson(1604));
            return true;
        }

        $res = UserModel::where('mobile', '=', $mobile)->update(['password' => md5($password0)]);
        if(!$res) {
            $this->setBody(Help::getJson(1605));
            return true;
        }

        $this->setBody(Help::getJson(1606));
        return true;
    }

}
