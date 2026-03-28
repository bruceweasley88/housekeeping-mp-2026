<?php

namespace app\api\controller;

use app\common\model\poster\Poster;
use think\response\Json;

/**
 * 海报接口
 * Class PosterController
 * @package app\api\controller
 */
class PosterController extends BaseApiController
{
    public array $notNeedLogin = ['lists'];

    /**
     * @notes 获取海报列表（首页轮播图）
     */
    public function lists(): Json
    {
        $list = Poster::where('is_show', 1)
            ->field('id,name,image,sort')
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return $this->data($list);
    }
}
