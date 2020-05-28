<?php


/**
 * @name MessageController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class MessageController extends BaseController
{
    public $messageType = ['普通', '图片', '视频', '投票'];


    /**
     * 发布一个信息
     * @return bool
     */
    public function addMessage() {

        $messageType = $this->getPost('message_type');
        if(!in_array($messageType, $this->messageType)) {
            $this->setBody(Help::getJson(1900));
            return true;
        }

        $categoryId = $this->getPost('category_id');
        if(!$categoryId || !is_numeric($categoryId)) {
            $this->setBody(Help::getJson(1901));
            return true;
        }

        $userId = $this->getPost('user_id');
        if(!$userId) {
            $this->setBody(Help::getJson(1902));
            return true;
        }

        $title = $this->getPost('title');
        if(!$title) {
            $this->setBody(Help::getJson(1904));
            return true;
        }

        if(mb_strlen($title) > 255) {
            $this->setBody(Help::getJson(1906));
            return true;
        }

        $other = $this->getPost('other');
        $content = $this->getPost('content');
        $dataTime = Help::getDayTime();

        $insertData = [
            'user_id' => $userId,
            'content' => $content,
            'like_num' => 0,
            'comment_num' => 0,
            'look_num' => 0,
            'hot_switch' => 0,
            'weigh' => 0,
            'show_switch' => 1,
            'created_at' => $dataTime,
            'updated_at' => $dataTime,
            'message_type' => $messageType,
            'title' => $title,
            'other' => $other,
            'category_id' => $categoryId,
        ];

        try {
            $res = MessageModel::insert($insertData);
            if(!$res) {
                $this->setBody(Help::getJson(1908));
                return true;
            }
        } catch (\Exception $e) {
            $this->setBody(Help::getJson(1910));
            return true;
        }

        $this->setBody(Help::getJson(1912));
        return true;
    }
}
