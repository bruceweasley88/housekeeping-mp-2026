<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\UserNotice;

/**
 * 用户公告逻辑层
 */
class NoticeLogic extends BaseLogic
{
    /**
     * 获取用户公告列表
     * @param int $userId 用户ID
     * @return array
     */
    public static function lists(int $userId): array
    {
        $list = UserNotice::where('user_id', $userId)
            ->order('create_time', 'desc')
            ->select()
            ->toArray();

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'content' => $item['content'],
                'time' => date('Y.m.d H:i', strtotime($item['create_time'])),
                'is_read' => $item['is_read'],
                'related_type' => $item['related_type'],
                'related_id' => $item['related_id'],
            ];
        }

        return $result;
    }

    /**
     * 获取公告详情（同时标记已读）
     * @param int $userId 用户ID
     * @param int $id 公告ID
     * @return array|false
     */
    public static function detail(int $userId, int $id)
    {
        if (empty($id)) {
            self::setError('参数错误');
            return false;
        }

        $notice = UserNotice::where('id', $id)
            ->where('user_id', $userId)
            ->findOrEmpty();

        if ($notice->isEmpty()) {
            self::setError('公告不存在');
            return false;
        }

        // 标记已读
        if ($notice->is_read == 0) {
            $notice->is_read = 1;
            $notice->save();
        }

        return [
            'id' => $notice->id,
            'title' => $notice->title,
            'content' => $notice->content,
            'time' => date('Y.m.d H:i', strtotime($notice->create_time)),
            'related_type' => $notice->related_type,
            'related_id' => $notice->related_id,
        ];
    }

    /**
     * 获取未读数量
     * @param int $userId 用户ID
     * @return int
     */
    public static function unreadCount(int $userId): int
    {
        return UserNotice::where('user_id', $userId)
            ->where('is_read', 0)
            ->count();
    }

    /**
     * 添加公告（供其他模块调用）
     * @param int $userId 用户ID
     * @param string $title 标题
     * @param string $content 内容
     * @param string $relatedType 关联类型
     * @param int $relatedId 关联ID
     * @return bool
     */
    public static function addNotice(
        int $userId,
        string $title,
        string $content,
        string $relatedType = '',
        int $relatedId = 0
    ): bool {
        try {
            UserNotice::create([
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'is_read' => 0,
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
