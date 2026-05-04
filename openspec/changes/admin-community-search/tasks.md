## 1. 后端 - 用户模型增加小区搜索支持

- [x] 1.1 在 `User` 模型中添加 `searchCommunityIdAttr` 搜索器，直接按 `user` 表的 `community_id` 字段筛选
- [x] 1.2 修改 `UserLists` 的 `setSearch()` 方法，将 `community_id` 加入允许搜索字段列表

## 2. 前端 - 业主认证页面增加小区搜索

- [x] 2.1 在 `user_verify/index.vue` 中导入 `getCommunityLists` API
- [x] 2.2 在搜索表单中增加小区下拉选择器（`el-select`），绑定 `queryParams.community_id`
- [x] 2.3 页面加载时调用 API 获取小区列表数据填充下拉选项
- [x] 2.4 验证：选择小区后列表正确筛选，清除后恢复全部数据

## 3. 前端 - 用户列表页面增加小区搜索

- [x] 3.1 在 `consumer/lists/index.vue` 中导入 `getCommunityLists` API
- [x] 3.2 在搜索表单中增加小区下拉选择器（`el-select`），绑定 `queryParams.community_id`
- [x] 3.3 页面加载时调用 API 获取小区列表数据填充下拉选项
- [x] 3.4 验证：选择小区后列表正确筛选，清除后恢复全部数据
