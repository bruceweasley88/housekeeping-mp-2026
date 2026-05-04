## ADDED Requirements

### Requirement: 平台统计周期筛选
系统 SHALL 提供 `GET /finance.bill/platformStats` 接口，接受 `period` 参数（枚举值：`week`/`month`/`year`），根据当前时间自动计算统计周期的起止时间。

#### Scenario: 按周统计
- **WHEN** 请求参数 `period=week`
- **THEN** 系统计算本周一 00:00:00 到当前时间作为统计周期，仅统计该时间范围内的数据

#### Scenario: 按月统计
- **WHEN** 请求参数 `period=month`
- **THEN** 系统计算本月 1 日 00:00:00 到当前时间作为统计周期

#### Scenario: 按年统计
- **WHEN** 请求参数 `period=year`
- **THEN** 系统计算本年 1 月 1 日 00:00:00 到当前时间作为统计周期

### Requirement: 平台提现总笔数和金额
系统 SHALL 在平台统计中返回指定周期内已提取（STATUS_SETTLED）的提现记录总笔数和总金额。

#### Scenario: 有已提取记录
- **WHEN** 周期内存在状态为已提取的提现记录
- **THEN** 返回 `settled_count`（笔数）和 `settled_amount`（金额，保留两位小数）

#### Scenario: 无已提取记录
- **WHEN** 周期内无已提取记录
- **THEN** 返回 `settled_count=0` 和 `settled_amount=0`

### Requirement: 平台拒绝提现笔数和金额
系统 SHALL 在平台统计中返回指定周期内已拒绝（STATUS_REJECTED）的提现记录总笔数和总金额。

#### Scenario: 有拒绝记录
- **WHEN** 周期内存在状态为已拒绝的提现记录
- **THEN** 返回 `rejected_count` 和 `rejected_amount`（保留两位小数）

### Requirement: 平台待提现金额
系统 SHALL 在平台统计中返回指定周期内待审核（STATUS_PENDING）的提现记录总笔数和总金额。

#### Scenario: 有待审核记录
- **WHEN** 周期内存在状态为审核中的提现记录
- **THEN** 返回 `pending_count` 和 `pending_amount`（保留两位小数）

### Requirement: 平台佣金收入金额
系统 SHALL 根据已提取金额和当前手续费率动态计算佣金收入，公式为：佣金 = settled_amount × withdraw_fee_rate / 100。

#### Scenario: 正常计算佣金
- **WHEN** 周期内已提取金额为 10000 元，手续费率为 3%
- **THEN** 返回 `commission_amount=300.00`

#### Scenario: 无已提取记录
- **WHEN** 周期内无已提取记录
- **THEN** 返回 `commission_amount=0`

### Requirement: 平台统计页面展示
Admin 端 SHALL 在"财务管理"菜单下新增"平台提现统计"页面，包含周期选择器（周/月/年切换）和统计卡片。

#### Scenario: 页面加载
- **WHEN** 管理员进入平台提现统计页面
- **THEN** 默认显示"月"维度的统计数据，展示已提取笔数/金额、已拒绝笔数/金额、待提现笔数/金额、佣金收入金额

#### Scenario: 切换周期
- **WHEN** 管理员点击"周"/"月"/"年"切换按钮
- **THEN** 统计数据刷新为对应周期的数据
