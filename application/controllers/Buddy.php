<?php
/**
 * @name BuddyController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */

use Illuminate\Database\Capsule\Manager as DB;

class BuddyController extends BaseController {


    private $likeTypeEnum = ['关注', '互关'];

    /**
     * 关注用户
     * @return bool
     */
    public function likeUserAction() {
        $fromUserId = $this->getPost('from_user_id'); // 我的ID
        $toUserId = $this->getPost('to_user_id');

        $userList = UserModel::whereIn('id', [$fromUserId, $toUserId])->get();
        if(count($userList) != 2) {
            $this->setBody(Help::getJson(2100));
            return true;
        }

        try {
//            DB::beginTransaction();
//            $likeList = BuddyModel::where(function($query) use ($fromUserId)
//            {
//                $query->where('from_user_id', '=', $fromUserId)
//                    ->orWhere('to_user_id', '=', $fromUserId);
//            })->where(function($query) use ($toUserId)
//            {
//                $query->where('from_user_id', '=', $toUserId)
//                    ->orWhere('to_user_id', '=', $toUserId);
//            })->lockForUpdate()->get();

            DB::beginTransaction();
            $myLike = BuddyModel::where([['from_user_id', '=', $fromUserId], ['to_user_id', '=', $toUserId]])->lockForUpdate()->first();
            if($myLike) { // 我已经关注此用户
                DB::rollBack();
                $this->setBody(Help::getJson(2102));
                return true;
            }

            $likeMe = BuddyModel::where([['from_user_id', '=', $toUserId], ['to_user_id', '=', $fromUserId]])->lockForUpdate()->first();

            $likeType = $this->likeTypeEnum[0];
            if($likeMe) { // 此用户已经关注我
                $likeType = $this->likeTypeEnum[1];
                $res = BuddyModel::where([['from_user_id', '=', $toUserId], ['to_user_id', '=', $fromUserId]])->update(['like_type' => $likeType]);
                if(!$res) {
                    DB::rollBack();
                    $this->setBody(Help::getJson(2106));
                    return true;
                }
            }

            $insertData = [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'like_type' => $likeType,
                'created_at' => Help::getDateTime(),
            ];
            $res = BuddyModel::insert($insertData);
            if(!$res) {
                DB::rollBack();
                $this->setBody(Help::getJson(2106));
                return true;
            }

            DB::commit();
            $this->setBody(Help::getJson(2104));
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setBody(Help::getJson(2108));
            return true;
        }
    }


    /**
     * 取关用户
     * @return bool
     */
    public function unLikeUserAction() {
        $fromUserId = $this->getPost('from_user_id'); // 我的ID
        $toUserId = $this->getPost('to_user_id');

        $userList = UserModel::whereIn('id', [$fromUserId, $toUserId])->get();
        if(count($userList) != 2) {
            $this->setBody(Help::getJson(2200));
            return true;
        }

        try {
            DB::beginTransaction();
            $myLike = BuddyModel::where([['from_user_id', '=', $fromUserId], ['to_user_id', '=', $toUserId]])->lockForUpdate()->first();
            if(!$myLike) {
                DB::rollBack();
                $this->setBody(Help::getJson(2202));
                return true;
            }

            $likeMe = BuddyModel::where([['from_user_id', '=', $toUserId], ['to_user_id', '=', $fromUserId]])->lockForUpdate()->first();
            if($likeMe) {
                $res = BuddyModel::where([['from_user_id', '=', $toUserId], ['to_user_id', '=', $fromUserId]])->update(['like_type' => $this->likeTypeEnum[0]]);
                if(!$res) {
                    DB::rollBack();
                    $this->setBody(Help::getJson(2206));
                    return true;
                }
            }

            $res = BuddyModel::where([['from_user_id', '=', $fromUserId], ['to_user_id', '=', $toUserId]])->delete();
            if(!$res) {
                DB::rollBack();
                $this->setBody(Help::getJson(2206));
                return true;
            }

            DB::commit();
            $this->setBody(Help::getJson(2204));
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->setBody(Help::getJson(2208));
            return true;
        }
    }


    /**
     * 获取我关注的用户
     * @return bool
     */
    public function listMyLikeAction() {
        $page = $this->getQuery('page');
        if(!$page || !is_numeric($page)) {
            $page = 1;
        }

        $take = $this->getQuery('take');
        if(!$take || !is_numeric($take)) {
            $take = 10;
        }

        $fromUserId = $this->getQuery('from_user_id'); // 我的ID

        $myLikeList = BuddyModel::where('from_user_id', '=', $fromUserId)->skip(Help::getSkip($page, $take))->take($take)->orderBy('created_at', 'desc')->get();
        $this->setBody(Help::getJson(2300, $myLikeList));
        return true;
    }


    /**
     * 获取关注我的用户
     * @return bool
     */
    public function listLikeMeAction() {
        Help::test();

        $page = $this->getQuery('page');
        if(!$page || !is_numeric($page)) {
            $page = 1;
        }

        $take = $this->getQuery('take');
        if(!$take || !is_numeric($take)) {
            $take = 10;
        }

        $toUserId = $this->getQuery('to_user_id');
        $likeMeList = BuddyModel::where('to_user_id', '=', $toUserId)->skip(Help::getSkip($page, $take))->take($take)->orderBy('created_at', 'desc')->get();
        $this->setBody(Help::getJson(2400, $likeMeList));
        return true;
    }



}
