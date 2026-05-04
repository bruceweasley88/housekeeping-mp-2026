## ADDED Requirements

### Requirement: 用户统计周期筛选
系统 SHALL 提供 `GET /finance.bill/userStats` 接口，接受 `period` 参数（枚举值：`week`/`month`/`year`），根据当前时间自动计算统计周期的起止时间。接口 SHALL 支持分页参数 `page_no` 和 `page_size`。

#### Scenario: 按周统计
- **WHEN** 请求参数 `period=week`
- **THEN** 系统计算本周一 00:00:00 到当前时间作为统计周期

#### Scenario: 按月统计
- **WHEN** 请求参数 `period=month`
- **THEN** 系统计算本月 1 日 00:00:00 到当前时间作为统计周期

#### Scenario: 按年统计
- **WHEN** 请求参数 `period=year`
- **THEN** 系统计算本年 1 月 1 日 00:00:00 到当前时间作为统计周期

### Requirement: 按用户聚合提现统计
系统 SHALL 以用户（`user_id`）为单位，在指定周期内聚合每个用户的提现笔数、提现金额、拒绝笔数、拒绝金额。返回数据 SHALL 包含用户昵称、手机号等基本信息。

#### Scenario: 多用户有提现记录
- **WHEN** 周期内有 3 个用户分别提交了提现申请
- **THEN** 返回 3 条用户统计记录，每条包含 `user_id`、`nickname`、`mobile`、`settled_count`、`settled_amount`、`rejected_count`、`rejected_amount`

#### Scenario: 用户只有拒绝记录
- **WHEN** 某用户在周期内仅有被拒绝的提现记录
- **THEN** 该用户仍出现在统计列表中，`settled_count=0`、`rejected_count` 和 `rejected_amount` 为实际值

### Requirement: 用户统计分页
系统 SHALL 对用户维度的统计结果支持分页，按提现金额降序排列。

#### Scenario: 分页请求
- **WHEN** 请求 `page_no=1&page_size=15`
- **THEN** 返回前 15 条用户统计记录，包含总数 `count`

### Requirement: 用户提现明细展开
系统 SHALL 提供 `GET /finance.bill/userStatsDetail` 接口，接受 `user_id` 和 `period` 参数，返回该用户在指定周期内的每条提现记录详情。

#### Scenario: 展开用户明细
- **WHEN** 管理员在用户统计列表中点击某用户行的展开按钮
- **THEN** 系统请求该用户的提现明细，返回包含 `id`、`bill_no`、`amount`、`status`、`remark`、`create_time` 的明细列表

#### Scenario: 无提现明细
- **WHEN** 某用户在周期内无提现记录
- **THEN** 返回空列表

### Requirement: 用户统计页面展示
Admin 端 SHALL 在"财务管理"菜单下新增"用户提现统计"页面，包含周期选择器、用户统计表格（支持分页）和可展开的明细行。

#### Scenario: 页面加载
- **WHEN** 管理员进入用户提现统计页面
- **THEN** 默认显示"月"维度的用户统计数据，按提现金额降序排列

#### Scenario: 切换周期
- **WHEN** 管理员切换周期为"周"
- **THEN** 用户统计列表刷新为本周数据

#### Scenario: 展开用户明细
- **WHEN** 管理员点击用户行的展开箭头
- **THEN** 展开该用户的提现明细表格，展示每笔提现的时间、金额、状态、备注
