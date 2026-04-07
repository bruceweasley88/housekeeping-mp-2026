<?php

namespace app\adminapi\logic\community;

use app\common\enum\CommunityEnum;
use app\common\logic\BaseLogic;
use app\common\model\Community;
use app\common\model\user\User;

/**
 * 小区管理逻辑
 * Class CommunityLogic
 * @package app\adminapi\logic\community
 */
class CommunityLogic extends BaseLogic
{
    /**
     * @notes 小区总览统计（全局数据，不参与筛选）
     * @return array
     */
    public static function stat(): array
    {
        return [
            'enabled_total' => Community::where('status', CommunityEnum::STATUS_ENABLE)->count(),
            'audit_total'   => Community::where('status', CommunityEnum::STATUS_AUDIT)->count(),
        ];
    }

    /**
     * @notes 添加小区
     * @param array $params
     */
    public static function add(array $params)
    {
        Community::create([
            'name' => $params['name'],
            'province' => $params['province'] ?? '',
            'city' => $params['city'] ?? '',
            'district' => $params['district'] ?? '',
            'address' => $params['address'] ?? '',
            'status' => CommunityEnum::STATUS_ENABLE,
        ]);
    }

    /**
     * @notes 编辑小区
     * @param array $params
     * @return bool
     */
    public static function edit(array $params): bool
    {
        try {
            Community::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'province' => $params['province'] ?? '',
                'city' => $params['city'] ?? '',
                'district' => $params['district'] ?? '',
                'address' => $params['address'] ?? '',
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除小区
     * @param array $params
     */
    public static function delete(array $params)
    {
        Community::destroy($params['id']);
    }

    /**
     * @notes 查看小区详情
     * @param $params
     * @return array
     */
    public static function detail($params): array
    {
        return Community::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 审核小区
     * @param array $params
     * @return bool
     */
    public static function audit(array $params): bool
    {
        try {
            $community = Community::findOrEmpty($params['id']);
            if ($community->isEmpty()) {
                throw new \Exception('小区不存在');
            }

            if ($community['status'] != CommunityEnum::STATUS_AUDIT) {
                throw new \Exception('该小区不是待审核状态');
            }

            $community->status = $params['status'];
            $community->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
