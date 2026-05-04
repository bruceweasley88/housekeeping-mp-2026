## 1. 后端 - 平台统计 API

- [x] 1.1 在 `BillController` 中新增 `platformStats` 方法，接收 `period` 参数，计算周期起止时间，聚合查询已提取/已拒绝/待审核的笔数和金额，动态计算佣金收入
- [x] 1.2 在 `app/adminapi/config/route.php` 中注册 `finance.bill/platformStats` 路由（自动路由，无需手动注册）

## 2. 后端 - 用户统计 API

- [x] 2.1 在 `BillController` 中新增 `userStats` 方法，接收 `period`/`page_no`/`page_size` 参数，按 `user_id` 分组聚合每个用户的提现/拒绝笔数和金额，关联用户表获取昵称和手机号
- [x] 2.2 在 `BillController` 中新增 `userStatsDetail` 方法，接收 `user_id`/`period` 参数，返回该用户在周期内的提现明细列表
- [x] 2.3 在路由文件中注册 `finance.bill/userStats` 和 `finance.bill/userStatsDetail` 路由（自动路由，无需手动注册）

## 3. Admin 前端 - 平台统计页面

- [x] 3.1 在 `admin/src/api/finance.ts` 中新增 `getPlatformStats` API 方法，调用 `/finance.bill/platformStats`
- [x] 3.2 创建 `admin/src/views/finance/withdraw_platform_stats.vue` 页面，包含周/月/年切换按钮和统计卡片（已提取笔数/金额、已拒绝笔数/金额、待提现笔数/金额、佣金收入）

## 4. Admin 前端 - 用户统计页面

- [x] 4.1 在 `admin/src/api/finance.ts` 中新增 `getUserStats` 和 `getUserStatsDetail` API 方法
- [x] 4.2 创建 `admin/src/views/finance/withdraw_user_stats.vue` 页面，包含周/月/年切换、用户统计表格（分页）、可展开行展示提现明细

## 5. 路由与菜单配置

- [x] 5.1 在 Admin 路由中注册"平台提现统计"和"用户提现统计"页面路由（`finance/withdraw_platform_stats`、`finance/withdraw_user_stats`），放在"财务管理"菜单下（后台菜单配置页面手动添加）
