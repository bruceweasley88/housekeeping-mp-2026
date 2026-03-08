<?php

namespace app\api\logic;

use app\common\enum\CommunityEnum;
use app\common\logic\BaseLogic;
use app\common\model\Community;
use app\common\model\user\User;

/**
 * 小区逻辑层
 * Class CommunityLogic
 * @package app\api\logic
 */
class CommunityLogic extends BaseLogic
{
    /**
     * @notes 获取小区列表
     * @param array $params
     * @return array
     */
    public static function lists(array $params): array
    {
        $where = [
            ['status', '=', CommunityEnum::STATUS_ENABLE]
        ];

        if (!empty($params['province'])) {
            $where[] = ['province', '=', $params['province']];
        }
        if (!empty($params['city'])) {
            $where[] = ['city', '=', $params['city']];
        }
        if (!empty($params['district'])) {
            $where[] = ['district', '=', $params['district']];
        }

        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 20;

        $count = Community::where($where)->count();
        $list = Community::where($where)
            ->field('id,name,address,province,city,district')
            ->page($page, $size)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        return [
            'list' => $list,
            'count' => $count,
        ];
    }

    /**
     * @notes 搜索小区
     * @param array $params
     * @return array
     */
    public static function search(array $params): array
    {
        $where = [
            ['status', '=', CommunityEnum::STATUS_ENABLE]
        ];

        if (!empty($params['keyword'])) {
            $where[] = ['name|address', 'like', '%' . $params['keyword'] . '%'];
        }
        if (!empty($params['province'])) {
            $where[] = ['province', '=', $params['province']];
        }
        if (!empty($params['city'])) {
            $where[] = ['city', '=', $params['city']];
        }
        if (!empty($params['district'])) {
            $where[] = ['district', '=', $params['district']];
        }

        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 20;

        $count = Community::where($where)->count();
        $list = Community::where($where)
            ->field('id,name,address,province,city,district')
            ->page($page, $size)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        return [
            'list' => $list,
            'count' => $count,
        ];
    }

    /**
     * @notes 申请添加小区
     * @param array $params
     * @return bool
     */
    public static function apply(array $params): bool
    {
        try {
            // 检查是否已存在同名小区
            $exists = Community::where('name', $params['name'])
                ->where('province', $params['province'])
                ->where('city', $params['city'])
                ->where('district', $params['district'])
                ->findOrEmpty();
            if (!$exists->isEmpty()) {
                throw new \Exception('该小区已存在');
            }

            // 创建申请记录
            Community::create([
                'name' => $params['name'],
                'province' => $params['province'],
                'city' => $params['city'],
                'district' => $params['district'],
                'address' => $params['address'] ?? '',
                'status' => CommunityEnum::STATUS_AUDIT,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 获取用户地址信息
     * @param int $userId
     * @return array
     */
    public static function getAddressInfo(int $userId): array
    {
        $user = User::where(['id' => $userId])
            ->field('community_id,building,room')
            ->findOrEmpty();

        if ($user->isEmpty() || empty($user['community_id'])) {
            return [];
        }

        $community = Community::where(['id' => $user['community_id']])
            ->field('id,name,address,province,city,district')
            ->findOrEmpty();

        if ($community->isEmpty()) {
            return [];
        }

        return [
            'community_id' => $community['id'],
            'community_name' => $community['name'],
            'community_address' => $community['address'],
            'province' => $community['province'],
            'city' => $community['city'],
            'district' => $community['district'],
            'building' => $user['building'],
            'room' => $user['room'],
        ];
    }

    /**
     * @notes 保存用户地址
     * @param int $userId
     * @param array $params
     * @return bool
     */
    public static function saveAddress(int $userId, array $params): bool
    {
        try {
            // 检查小区是否存在且已启用
            $community = Community::where(['id' => $params['community_id']])
                ->findOrEmpty();
            if ($community->isEmpty()) {
                throw new \Exception('小区不存在');
            }
            if ($community['status'] != CommunityEnum::STATUS_ENABLE) {
                throw new \Exception('该小区暂未启用');
            }

            // 更新用户地址
            User::update([
                'id' => $userId,
                'community_id' => $params['community_id'],
                'building' => $params['building'] ?? '',
                'room' => $params['room'] ?? '',
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
