<?php

/**
 * @name CategoryController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class CategoryController extends BaseController {

    /**
     * 获取分类列表
     * @return bool
     */
    public function listCategoryAction() {
        $categoryList = Help::getRedis()->get(RedisCacheKey::$CATEGORY_ALL);
        if(!$categoryList) {
            $list = CategoryModel::where('show_switch', '=', 1)->orderBy('weigh', 'desc')->get();
            $list = json_decode(json_encode($list), true);
            $list = self::treeData($list);
            $list['cached_at'] = Help::getDateTime();
            $categoryList = Help::getJson(2000, $list);
            Help::getRedis()->set(RedisCacheKey::$CATEGORY_ALL, $categoryList, 60);
        }
        $this->setBody($categoryList);
        return true;
    }


    /**
     * 把分类处理成树形
     * @param $data
     * @param int $pid
     * @return array
     */
    private static function treeData($data, $pid = 0) {
        $res = [];

        foreach ($data as $k => $v) {
            if($v['pid'] == $pid) {
                unset($data[$k]);
                $v['child'][] = self::treeData($data, $v['id']);
                $res[] = $v;
            }
        }

        return $res;
    }


}
