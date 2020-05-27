<?php

class Code {

    /**
     * 返回码
     * @var array
     */
    public static $code =  [
        // bannerList
        1000 => '获取bannerList成功',

        // listHotMessageAction
        1100 => '帖子列表获取成功',

        // infoMessageAction
        1200 => '帖子内容获取成功',

        // likeMessageAction
        1300 => '帖子不存在',
        1301 => '帖子点赞成功',

        // passwordLoginAction
        1400 => '手机号输入有误',
        1401 => '密码输入有误',
        1402 => '手机号没有注册',
        1403 => '密码错误',
        1404 => '登陆成功',

        // getResetPasswordCodeAction
        1500 => '手机号输入有误',
        1501 => '手机号没有注册',
        1502 => '系统异常：验证码发送失败',
        1503 => '系统异常：Redis存储验证码失败',
        1504 => '验证码已经发送',

        // resetPasswordAction
        1600 => '手机号输入有误',
        1601 => '验证输入有误',
        1602 => '密码输入有误',
        1603 => '两次输入的密码不一致',
        1604 => '验证码验证码失败',
        1605 => '系统异常：密码重置失败',
        1606 => '密码重置成功',


        // getRegisterCodeAction
        1700 => '手机号输入有误',
        1701 => '系统异常：验证码发送失败',
        1702 => '系统异常：Redis存储验证码失败',
        1703 => '验证码已经发送',

        // registerAction
        1800 => '手机号输入有误',
        1801 => '验证输入有误',
        1802 => '头像选择有误',
        1803 => '昵称不能为空',
        1810 => '简介输入有误',
        1811 => '密码输入有误',
        1812 => '两次输入的密码不一致',
        1813 => '验证码验证码失败',
        1814 => '系统异常：注册失败',
        1815 => '注册成功',
    ];

}
