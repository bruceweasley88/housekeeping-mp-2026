<?php

namespace app\adminapi\lists\finance;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\BillEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\Bill;
use app\common\service\FileService;

/**
 * 提现记录列表
 */
class BillWithdrawLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * 搜索条件
     */
    public function setSearch(): array
    {
        return [
            '=' => ['b.status'],
        ];
    }

    /**
     * 自定义查询条件
     */
    public function queryWhere()
    {
        $where = [];
        // 固定条件：仅支出类型
        $where[] = ['b.type', '=', BillEnum::TYPE_EXPENSE];

        if (!empty($this->params['user_info'])) {
            $where[] = ['u.nickname|u.mobile', 'like', '%' . $this->params['user_info'] . '%'];
        }

        if (!empty($this->params['start_time'])) {
            $where[] = ['b.create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (!empty($this->params['end_time'])) {
            $where[] = ['b.create_time', '<=', strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * 获取列表
     */
    public function lists(): array
    {
        $field = 'b.id,b.bill_no,b.amount,b.status,b.remark,b.service_rate,b.create_time,'
            . 'u.nickname,u.mobile,'
            . 'ub.real_name as bank_real_name,ub.bankcard_image';

        $lists = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->leftJoin('user_bankcard ub', 'ub.user_id = b.user_id AND ub.status = 1 AND ub.delete_time IS NULL')
            ->field($field)
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->order('b.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['bankcard_image'] = FileService::getFileUrl($item['bankcard_image']);
            $item['status_desc'] = BillEnum::getStatusDesc($item['status']);
            $item['create_time_desc'] = date('Y-m-d H:i:s', strtotime($item['create_time']));
        }

        return $lists;
    }

    /**
     * 获取数量
     */
    public function count(): int
    {
        return Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->count();
    }
}
