<?php
/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */

class IndexController extends BaseController {

	/**
     * 默认动作
     * Yaf支持直接把\Yaf\Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/yaf_skeleton/index/index/index/name/root 的时候, 你就会发现不同
     */
	public function indexAction() {
        $this->setBody('This is yaf');
        return true;
	}



    // 获取首页banner
    public function listBannerAction() {
	    $take = $this->getQuery('take');
	    if(!$take || !is_numeric($take)) {
            $take = 5;
        }

        $cacheKey = $this->getCachePrefix();
        $bannerList = Help::getRedis()->get($cacheKey);
	    if($bannerList) {
            $this->setBody($bannerList);
            return true;
        }

	    $where = [['show_switch', '=' ,1]];
	    $bannerList = BannerModel::where($where)->take($take)->orderBy('weigh', 'desc')->get();
	    $json = Help::getJson(1000, $bannerList);
        Help::getRedis()->set($cacheKey, $json, 60);
        $this->setBody($json);
        return true;
    }



    // 获取帖子列表
    public function listHotMessageAction() {
        $page = $this->getQuery('page');
        if(!$page || !is_numeric($page)) {
            $page = 1;
        }

        $take = $this->getQuery('take');
        if(!$take || !is_numeric($take)) {
            $take = 10;
        }

        $cacheKey = $this->getCachePrefix().$page;
        $messageList = Help::getRedis()->get($cacheKey);
        if($messageList) {
            $this->setBody($messageList);
            return true;
        }

        $where = [['message.show_switch', '=', 1], ['message.hot_switch', '=', 1]];
        $messageList = MessageModel::where($where)->leftJoin('user', 'user.id', '=', 'message.user_id')->skip($this->getSkip($page, $take))->take($take)->orderBy('message.weigh', 'desc')->get();
        $json = Help::getJson(1100, $messageList);
        Help::getRedis()->set($cacheKey, $json, 60);
        $this->setBody($json);
        return true;
    }



    // 帖子内容
    public function infoMessageAction() {
        $messageId = $this->getQuery('message_id');
        if(!$messageId || !is_numeric($messageId)) {
            $this->setBody('');
            return true;
        }

        $cacheKey = $this->getCachePrefix().$messageId;
        $messageInfo = Help::getRedis()->get($cacheKey);
        if(!$messageInfo) {
            $messageInfo = MessageModel::whereId($messageId)->first();
            $messageInfo = Help::getJson(1200, $messageInfo);
            Help::getRedis()->set($cacheKey, $messageInfo, 60);
        }

        if($messageInfo) {
            $logArr = [$messageId];
            Help::writeLog('lookNum', date('Y_m_d_H_i'), $logArr);
        }

        $this->setBody($messageInfo);
        return true;
    }



    // 点赞帖子
    public function likeMessageAction() {
        $messageId = $this->getPost('message_id');
        if(!$messageId || !is_numeric($messageId)) {
            $this->setBody(Help::getJson(1300));
            return true;
        }

        $logArr = [$messageId];
        if($this->userInfo) {
            $logArr[] = $this->userInfo['id'];
        } else {
            $logArr[] = ' ';
        }
        Help::writeLog('likeNum', date('Y_m_d_H_i'), $logArr);
        $this->setBody(Help::getJson(1301));
        return true;
    }



    public function addMessageAction() {
        $hosts = [
            '47.114.180.172:9200',         // IP + Port
        ];
        $client = ClientBuilder::create()->setHosts($hosts)->setRetries(0)->build();

        $params = [
            'index' => ['message'],
            'type' => 'message',
            'body' => [
                'query' => [
                    'match' => [ 'content' => '测试' ],
                ]
            ]
        ];

        $response = $client->search($params);
        print_r($response);
        return true;
    }

}
