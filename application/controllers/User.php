<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class UserController extends BaseController {

    // 点赞帖子
    public function likeMessageAction() {
        $messageId = $this->getPost('message_id');
        if(!$messageId || !is_numeric($messageId)) {
            $this->setBody($this->getJson(1300));
            return true;
        }

        $logArr = [$messageId];
        if($this->userInfo) {
            $logArr[] = $this->userInfo['id'];
        } else {
            $logArr[] = ' ';
        }
        $this->writeLog('like_message_'.date('Y_m_d_H_i').'.log', $logArr);
        $this->setBody($this->getJson(1301));
        return true;
    }



}
