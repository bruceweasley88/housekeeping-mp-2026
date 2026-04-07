<?php

namespace app\adminapi\controller\trade_statistics;

use app\adminapi\controller\BaseAdminController;
use think\facade\Db;

/**
 * 交易统计控制器
 */
class IndexController extends BaseAdminController
{
    /**
     * 交易统计汇总（成交趋势 + 类型明细 + 发布中统计）
     */
    public function summary()
    {
        $params = $this->request->only([
            'date_type' => 'week',
            'start_date' => '',
            'end_date' => ''
        ]);

        [$startTime, $endTime] = $this->parseDateRange(
            $params['date_type'],
            $params['start_date'],
            $params['end_date']
        );

        return $this->data([
            'trend'           => $this->buildTrend($startTime, $endTime, $params['date_type']),
            'category_detail' => $this->buildCategoryDetail($startTime, $endTime),
            'publishing'      => $this->buildPublishing(),
        ]);
    }

    /**
     * 小区成交统计（Top10图表 + 分页表格）
     */
    public function communityStats()
    {
        $params = $this->request->only([
            'date_type' => 'week',
            'start_date' => '',
            'end_date' => ''
        ]);
        $page = $this->request->param('page', 1, 'intval');
        $pageSize = $this->request->param('page_size', 15, 'intval');

        [$startTime, $endTime] = $this->parseDateRange(
            $params['date_type'],
            $params['start_date'],
            $params['end_date']
        );

        return $this->data($this->buildCommunityStats($startTime, $endTime, $page, $pageSize));
    }

    /**
     * 解析日期范围
     */
    private function parseDateRange(string $dateType, string $startDate, string $endDate): array
    {
        switch ($dateType) {
            case 'week':
                $start = strtotime('monday this week');
                $end = time();
                break;
            case 'month':
                $start = strtotime('first day of this month 00:00:00');
                $end = time();
                break;
            case 'year':
                $start = strtotime('first day of January this year 00:00:00');
                $end = time();
                break;
            case 'custom':
            default:
                $start = $startDate ? strtotime($startDate) : strtotime('-30 days');
                $end = $endDate ? strtotime($endDate . ' 23:59:59') : time();
                break;
        }

        return [$start, $end];
    }

    /**
     * 成交趋势（按天/月分组）
     */
    private function buildTrend(int $start, int $end, string $dateType): array
    {
        $isYear = ($dateType === 'year');

        if ($isYear) {
            $dateFormat = '%Y-%m';
            $selectFormat = "DATE_FORMAT(FROM_UNIXTIME(update_time), '%Y-%m') AS date_label";
        } else {
            $dateFormat = '%m-%d';
            $selectFormat = "DATE_FORMAT(FROM_UNIXTIME(update_time), '%m-%d') AS date_label";
        }

        $list = Db::table('la_demand')
            ->field($selectFormat . ', COUNT(*) AS count, COALESCE(SUM(amount), 0) AS amount')
            ->where('status', 6)
            ->where('update_time', 'between', [$start, $end])
            ->whereNull('delete_time')
            ->group('date_label')
            ->order('update_time ASC')
            ->select()
            ->toArray();

        // 生成完整日期序列并填充空缺
        $dateMap = [];
        foreach ($list as $item) {
            $dateMap[$item['date_label']] = $item;
        }

        $dates = [];
        $counts = [];
        $amounts = [];
        $totalCount = 0;
        $totalAmount = 0;

        if ($isYear) {
            // 按月补全
            $current = strtotime(date('Y-01', $start));
            $endMonth = strtotime(date('Y-m', $end));
            while ($current <= $endMonth) {
                $label = date('Y-m', $current);
                $dates[] = $label;
                $item = $dateMap[$label] ?? null;
                $count = $item ? (int)$item['count'] : 0;
                $amount = $item ? (float)$item['amount'] : 0;
                $counts[] = $count;
                $amounts[] = $amount;
                $totalCount += $count;
                $totalAmount += $amount;
                $current = strtotime('+1 month', $current);
            }
        } else {
            // 按天补全
            $current = strtotime(date('Y-m-d', $start));
            $endDay = strtotime(date('Y-m-d', $end));
            while ($current <= $endDay) {
                $label = date('m-d', $current);
                $dates[] = $label;
                $item = $dateMap[$label] ?? null;
                $count = $item ? (int)$item['count'] : 0;
                $amount = $item ? (float)$item['amount'] : 0;
                $counts[] = $count;
                $amounts[] = $amount;
                $totalCount += $count;
                $totalAmount += $amount;
                $current = strtotime('+1 day', $current);
            }
        }

        return [
            'summary' => [
                'total_count' => $totalCount,
                'total_amount' => round($totalAmount, 2),
            ],
            'dates' => $dates,
            'counts' => $counts,
            'amounts' => $amounts,
        ];
    }

    /**
     * 按任务类型归类统计
     */
    private function buildCategoryDetail(int $start, int $end): array
    {
        return Db::table('la_demand')
            ->alias('d')
            ->field('dc.name AS category_name, COUNT(*) AS count, COALESCE(SUM(d.amount), 0) AS amount, ROUND(COALESCE(AVG(d.amount), 0), 2) AS avg_amount')
            ->leftJoin('la_demand_category dc', 'd.category_id = dc.id')
            ->where('d.status', 6)
            ->where('d.update_time', 'between', [$start, $end])
            ->whereNull('d.delete_time')
            ->group('d.category_id')
            ->order('count DESC')
            ->select()
            ->toArray();
    }

    /**
     * 发布中任务统计（当前快照）
     */
    private function buildPublishing(): array
    {
        $totalCount = Db::table('la_demand')
            ->where('status', 'in', [1, 2])
            ->whereNull('delete_time')
            ->count();

        $list = Db::table('la_demand')
            ->alias('d')
            ->field('dc.name AS category_name, COUNT(*) AS count')
            ->leftJoin('la_demand_category dc', 'd.category_id = dc.id')
            ->where('d.status', 'in', [1, 2])
            ->whereNull('d.delete_time')
            ->group('d.category_id')
            ->order('count DESC')
            ->select()
            ->toArray();

        return [
            'total_count' => $totalCount,
            'by_category' => $list,
        ];
    }

    /**
     * 小区成交统计
     */
    private function buildCommunityStats(int $start, int $end, int $page, int $pageSize): array
    {
        // 全量数据（按金额降序）
        $allList = Db::table('la_demand')
            ->alias('d')
            ->field('c.name AS community_name, COUNT(*) AS count, COALESCE(SUM(d.amount), 0) AS amount')
            ->leftJoin('la_community c', 'd.community_id = c.id')
            ->where('d.status', 6)
            ->where('d.update_time', 'between', [$start, $end])
            ->whereNull('d.delete_time')
            ->group('d.community_id')
            ->order('amount DESC')
            ->select()
            ->toArray();

        $totalCount = 0;
        $totalAmount = 0;
        foreach ($allList as $item) {
            $totalCount += (int)$item['count'];
            $totalAmount += (float)$item['amount'];
        }

        // 图表数据: Top 10 + 其他
        $chartList = [];
        $topList = array_slice($allList, 0, 10);
        if (count($allList) > 10) {
            $otherCount = 0;
            $otherAmount = 0;
            foreach (array_slice($allList, 10) as $item) {
                $otherCount += (int)$item['count'];
                $otherAmount += (float)$item['amount'];
            }
            $topList[] = [
                'community_name' => '其他',
                'count' => $otherCount,
                'amount' => round($otherAmount, 2),
            ];
        }
        $chartList = $topList;

        // 分页表格数据
        $offset = ($page - 1) * $pageSize;
        $tableList = array_slice($allList, $offset, $pageSize);

        return [
            'chart_list' => $chartList,
            'table_list' => $tableList,
            'count' => count($allList),
            'summary' => [
                'total_count' => $totalCount,
                'total_amount' => round($totalAmount, 2),
            ],
        ];
    }
}
