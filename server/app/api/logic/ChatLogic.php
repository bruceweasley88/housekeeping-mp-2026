<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\ChatSession;
use app\common\model\ChatMessage;
use app\common\model\user\User;
use app\common\service\FileService;

/**
 * 聊天逻辑层
 */
class ChatLogic extends BaseLogic
{
    /**
     * 获取会话列表
     * @param int $userId 当前用户ID
     * @return array
     */
    public static function getSessionList(int $userId): array
    {
        // 查询用户参与的所有会话
        $sessions = ChatSession::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->whereOr('user2_id', $userId);
        })
            ->order('update_time', 'desc')
            ->select()
            ->toArray();

        $result = [];
        foreach ($sessions as $session) {
            // 确定对方用户ID
            $peerUserId = $session['user1_id'] == $userId ? $session['user2_id'] : $session['user1_id'];

            // 获取对方用户信息
            $peerUser = User::where('id', $peerUserId)->findOrEmpty();
            if ($peerUser->isEmpty()) {
                continue;
            }

            // 获取最后一条消息
            $lastMessage = ChatMessage::where('session_id', $session['id'])
                ->order('id', 'desc')
                ->findOrEmpty();

            // 获取未读数量
            $unreadCount = ChatMessage::where('session_id', $session['id'])
                ->where('receiver_id', $userId)
                ->where('is_read', 0)
                ->count();

            $result[] = [
                'session_id' => $session['id'],
                'peer_user' => [
                    'id' => $peerUser->id,
                    'nickname' => $peerUser->nickname,
                    'avatar' => FileService::getFileUrl($peerUser->avatar),
                ],
                'last_message' => $lastMessage->isEmpty() ? null : [
                    'content' => $lastMessage->content,
                    'create_time' => strtotime($lastMessage->create_time),
                ],
                'unread_count' => $unreadCount,
                'update_time' => strtotime($session['update_time']),
            ];
        }

        return $result;
    }

    /**
     * 创建/获取会话
     * @param int $userId 当前用户ID
     * @param int $peerUserId 对方用户ID
     * @return array|false
     */
    public static function createSession(int $userId, int $peerUserId)
    {
        if (empty($peerUserId)) {
            self::setError('参数错误');
            return false;
        }

        if ($userId == $peerUserId) {
            self::setError('不能与自己创建会话');
            return false;
        }

        // 检查对方用户是否存在
        $peerUser = User::where('id', $peerUserId)->findOrEmpty();
        if ($peerUser->isEmpty()) {
            self::setError('用户不存在');
            return false;
        }

        // 确定user1_id（较小的ID）和user2_id（较大的ID）
        $user1Id = min($userId, $peerUserId);
        $user2Id = max($userId, $peerUserId);

        // 查找或创建会话
        $session = ChatSession::where('user1_id', $user1Id)
            ->where('user2_id', $user2Id)
            ->findOrEmpty();

        if ($session->isEmpty()) {
            $session = ChatSession::create([
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
            ]);
        }

        return [
            'session_id' => $session->id,
        ];
    }

    /**
     * 发送消息
     * @param int $userId 当前用户ID
     * @param int $sessionId 会话ID
     * @param string $content 消息内容
     * @return array|false
     */
    public static function sendMessage(int $userId, int $sessionId, string $content)
    {
        if (empty($sessionId) || empty($content)) {
            self::setError('参数错误');
            return false;
        }

        // 验证会话
        $session = ChatSession::where('id', $sessionId)->findOrEmpty();
        if ($session->isEmpty()) {
            self::setError('会话不存在');
            return false;
        }

        // 验证用户是否属于该会话
        if ($session->user1_id != $userId && $session->user2_id != $userId) {
            self::setError('无权操作该会话');
            return false;
        }

        // 确定接收者ID
        $receiverId = $session->user1_id == $userId ? $session->user2_id : $session->user1_id;

        // 创建消息
        $message = ChatMessage::create([
            'session_id' => $sessionId,
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'content' => $content,
            'is_read' => 0,
        ]);

        // 更新会话时间
        $session->save(['update_time' => date('Y-m-d H:i:s')]);

        return [
            'message_id' => $message->id,
            'create_time' => strtotime($message->create_time),
        ];
    }

    /**
     * 获取消息列表
     * @param int $userId 当前用户ID
     * @param int $sessionId 会话ID
     * @param int $lastId 上次获取的最后消息ID（增量获取）
     * @param int $page 页码
     * @param int $pageSize 每页数量
     * @return array|false
     */
    public static function getMessageList(int $userId, int $sessionId, int $lastId = 0, int $page = 1, int $pageSize = 20)
    {
        if (empty($sessionId)) {
            self::setError('参数错误');
            return false;
        }

        // 验证会话
        $session = ChatSession::where('id', $sessionId)->findOrEmpty();
        if ($session->isEmpty()) {
            self::setError('会话不存在');
            return false;
        }

        // 验证用户是否属于该会话
        if ($session->user1_id != $userId && $session->user2_id != $userId) {
            self::setError('无权操作该会话');
            return false;
        }

        // 确定对方用户ID
        $peerUserId = $session->user1_id == $userId ? $session->user2_id : $session->user1_id;

        // 获取当前用户头像
        $currentUser = User::where('id', $userId)->findOrEmpty();
        $currentAvatar = $currentUser->isEmpty() ? '' : FileService::getFileUrl($currentUser->avatar);

        // 获取对方用户头像
        $peerUser = User::where('id', $peerUserId)->findOrEmpty();
        $peerAvatar = $peerUser->isEmpty() ? '' : FileService::getFileUrl($peerUser->avatar);

        // 查询消息
        $query = ChatMessage::where('session_id', $sessionId);

        if ($lastId > 0) {
            // 增量获取：只获取ID更大的消息
            $query->where('id', '>', $lastId);
            $messages = $query->order('id', 'asc')->select()->toArray();
            $hasMore = false;
        } else {
            // 分页获取历史消息
            $total = $query->count();
            $messages = $query->order('id', 'desc')
                ->page($page, $pageSize)
                ->select()
                ->toArray();
            $messages = array_reverse($messages); // 反转，使消息按时间正序排列
            $hasMore = ($page * $pageSize) < $total;
        }

        // 格式化消息
        $list = [];
        foreach ($messages as $msg) {
            $isSelf = $msg['sender_id'] == $userId;
            $list[] = [
                'id' => $msg['id'],
                'sender_id' => $msg['sender_id'],
                'content' => $msg['content'],
                'is_self' => $isSelf,
                'avatar' => $isSelf ? $currentAvatar : $peerAvatar,
                'create_time' => strtotime($msg['create_time']),
                'create_time_text' => self::formatTime(strtotime($msg['create_time'])),
            ];
        }

        return [
            'list' => $list,
            'has_more' => $hasMore,
        ];
    }

    /**
     * 标记消息已读
     * @param int $userId 当前用户ID
     * @param int $sessionId 会话ID
     * @return bool
     */
    public static function markAsRead(int $userId, int $sessionId): bool
    {
        if (empty($sessionId)) {
            self::setError('参数错误');
            return false;
        }

        // 验证会话
        $session = ChatSession::where('id', $sessionId)->findOrEmpty();
        if ($session->isEmpty()) {
            self::setError('会话不存在');
            return false;
        }

        // 验证用户是否属于该会话
        if ($session->user1_id != $userId && $session->user2_id != $userId) {
            self::setError('无权操作该会话');
            return false;
        }

        // 标记该会话中接收者为当前用户的未读消息为已读
        ChatMessage::where('session_id', $sessionId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return true;
    }

    /**
     * 格式化时间显示
     * @param int $timestamp 时间戳
     * @return string
     */
    private static function formatTime(int $timestamp): string
    {
        $now = time();
        $diff = $now - $timestamp;

        // 1分钟内：刚刚
        if ($diff < 60) {
            return '刚刚';
        }

        // 1小时内：X分钟前
        if ($diff < 3600) {
            return floor($diff / 60) . '分钟前';
        }

        $todayStart = strtotime(date('Y-m-d'));
        $yesterdayStart = strtotime('-1 day', $todayStart);

        // 今天：HH:mm
        if ($timestamp >= $todayStart) {
            return date('H:i', $timestamp);
        }

        // 昨天：昨天 HH:mm
        if ($timestamp >= $yesterdayStart) {
            return '昨天 ' . date('H:i', $timestamp);
        }

        // 今年：MM-DD HH:mm
        if (date('Y') == date('Y', $timestamp)) {
            return date('m-d H:i', $timestamp);
        }

        // 往年：YYYY-MM-DD HH:mm
        return date('Y-m-d H:i', $timestamp);
    }
}
