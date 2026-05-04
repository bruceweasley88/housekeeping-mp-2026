## Context

Admin 管理后台的用户列表和业主认证页面需要按小区筛选功能。当前状态：

- **用户列表**：`user` 表已有 `community_id` 字段（UniApp 首页选择小区时通过 `/user/addressSave` 写入），但后端 `UserLists` 类未支持按此字段筛选。
- **业主认证**：后端 `UserVerifyLists` 已支持 `community_id` 精确搜索，但前端没有对应的选择器组件。
- **小区数据**：已有 `Community` 模型和 `/community.community/lists` API，可复用为下拉数据源。

## Goals / Non-Goals

**Goals:**
- 在用户列表和业主认证页面的搜索栏增加小区下拉选择器
- 选择小区后实时筛选并显示对应数据
- 后端直接通过 `user` 表的 `community_id` 字段筛选用户

**Non-Goals:**
- 不修改数据库表结构
- 不新增 API 端点（复用现有小区列表 API）
- 不涉及小程序端变更

## Decisions

### 1. 用户列表按小区筛选：直接使用 user 表的 community_id 字段

**选择**：在 `UserLists` 中通过 `user` 表自身的 `community_id` 字段筛选。

**理由**：UniApp 首页选择小区时已将 `community_id` 写入 `user` 表（`CommunityLogic::saveAddress`），数据已就绪，无需 JOIN 其他表，查询最简单高效。

**备选方案**：通过 `user_verify` 表 JOIN 查询 → 拒绝，因为 user 表已有该字段，JOIN 是多余的。

### 2. 小区下拉数据源：复用现有 community API

**选择**：前端直接调用 `getCommunityLists` API（已有 `admin/src/api/community.ts`）加载小区列表。

**理由**：API 已存在，返回小区 id 和 name，完全满足下拉选择需求。

### 3. 下拉选择器组件：使用 el-select

**选择**：使用 `el-select` 组件，初始加载全部小区列表，支持 clearable 清除选择。

**理由**：小区数量通常有限（几十到几百个），初始加载全部数据不会造成性能问题。

## Risks / Trade-offs

- **[数据覆盖范围] user.community_id 来自用户自选，user_verify.community_id 来自认证审核** → 两个来源可能不同。用户列表按 `user.community_id` 筛选反映的是用户当前选择的小区，业主认证按 `user_verify.community_id` 筛选反映的是认证通过的小区，语义不同但各自正确。
