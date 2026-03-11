<?php

namespace app\common\enum;

/**
 * 需求状态枚举
 */
class DemandEnum
{
    // 状态常量
    const STATUS_PENDING = 1;     // 待接单
    const STATUS_ONGOING = 2;     // 进行中
    const STATUS_COMPLETED = 3;   // 已完成
    const STATUS_CANCELLED = 4;   // 已取消
    const STATUS_OFFLINE = 5;     // 已下架
    const STATUS_SETTLED = 6;     // 已结算

    /**
     * 获取状态描述
     */
    public static function getStatusDesc($value = true)
    {
        $data = [
            self::STATUS_PENDING => '待接单',
            self::STATUS_ONGOING => '进行中',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_OFFLINE => '已下架',
            self::STATUS_SETTLED => '已结算',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * 获取状态提示文案（用于前端状态卡片显示）
     * @param int $status 状态值
     * @param string $role 角色：publisher=发布者，acceptor=承接者
     * @return array ['title' => '标题', 'desc' => '描述']
     */
    public static function getStatusTips(int $status, string $role): array
    {
        $tips = [
            'publisher' => [
                self::STATUS_PENDING => [
                    'title' => '需求已发布',
                    'desc' => '等待需求被承接，承接后将进行站内通知',
                ],
                self::STATUS_ONGOING => [
                    'title' => '需求进行中',
                    'desc' => '需求已被承接，请和承接人沟通后完成需求',
                ],
                self::STATUS_COMPLETED => [
                    'title' => '需求已完成',
                    'desc' => '确认需求已完成，进行平台结算金额支付',
                ],
                self::STATUS_SETTLED => [
                    'title' => '需求已结算',
                    'desc' => '需求已成功结算，欢迎再次发布新需求',
                ],
            ],
            'acceptor' => [
                self::STATUS_ONGOING => [
                    'title' => '需求已承接',
                    'desc' => '承接后，点击立即沟通/打电话可与需求方联系',
                ],
                self::STATUS_COMPLETED => [
                    'title' => '需求已完成',
                    'desc' => '需求完成后，等待需求方进行金额结算',
                ],
                self::STATUS_SETTLED => [
                    'title' => '需求已结算',
                    'desc' => '金额已打款到您的账号，在我的账单可查看',
                ],
            ],
        ];

        return $tips[$role][$status] ?? ['title' => '', 'desc' => ''];
    }
}
