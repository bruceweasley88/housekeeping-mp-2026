## Why

Admin 管理后台的"用户列表"和"业主认证"页面缺少按小区筛选的功能。运营人员需要手动翻页查找某个小区的用户或认证记录，无法快速查看各小区的用户数和认证情况，降低了管理效率。

## What Changes

- **用户列表页面**：增加小区下拉选择器，支持按小区筛选用户。选择小区后实时显示该小区的用户列表及总数。
- **业主认证页面**：增加小区下拉选择器，支持按小区筛选认证记录。选择小区后实时显示该小区的认证数量和认证信息。
- **后端 UserLists**：新增 `community_id` 搜索条件，直接使用 `user` 表的 `community_id` 字段筛选。
- **后端 User 模型**：新增 `searchCommunityIdAttr` 搜索器。

## Capabilities

### New Capabilities
- `community-user-filter`: 用户列表页面增加小区下拉搜索功能，后端直接通过 user 表的 community_id 字段筛选
- `community-verify-filter`: 业主认证页面增加小区下拉搜索功能（后端已支持 community_id 搜索，仅需前端增加选择器）

### Modified Capabilities

## Impact

- **前端页面**：`admin/src/views/consumer/lists/index.vue`（用户列表）、`admin/src/views/user_verify/index.vue`（业主认证）
- **后端列表类**：`server/app/adminapi/lists/user/UserLists.php`（新增 community_id 搜索）
- **后端模型**：`server/app/common/model/user/User.php`（新增 community_id 搜索器）
- **API**：复用现有 `/community.community/lists` 接口获取小区下拉数据，无需新增 API
