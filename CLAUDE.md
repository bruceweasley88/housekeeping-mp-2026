# 家政项目

## 1. 项目概述
- **后端**: ThinkPHP 8.0
- **管理后台**: Vue 3 + Element Plus
- **小程序端**: UniApp（微信小程序）

## 2. 技术栈

### Server（后端）
- **框架**: ThinkPHP 8.0
- **语言**: PHP >= 8.0
- **数据库**: MySQL
- **主要依赖**:
  - `topthink/think-orm`: ORM
  - `w7corp/easywechat`: 微信 SDK

### Admin（管理后台）
- **框架**: Vue 3.5 + Vite
- **UI 库**: Element Plus 2.9
- **CSS**: Tailwind CSS + SCSS
- **状态管理**: Pinia
- **路由**: Vue Router
- **HTTP 客户端**: Axios

### Uniapp（小程序）
- **框架**: UniApp（基于 Vue 3）
- **UI 库**: vk-uview-ui
- **CSS**: Tailwind CSS + SCSS
- **平台**: 微信小程序、H5、Android/iOS App

## 3. 目录结构

### 根目录
- `server/`: PHP 后端代码
- `admin/`: Vue 3 管理后台源码
- `uniapp/`: 小程序源码

### Server 目录结构 (`server/`)
- `app/`: 应用逻辑
  - `adminapi/`: 管理后台 API（控制器、逻辑层、服务层）
  - `api/`: 客户端 API（小程序端/PC端）
  - `common/`: 公共逻辑（模型、枚举、工具类）
    - `model/` - 数据模型（用户、管理员、文章、文件、支付、通知、装修、字典等）
    - `enum/` - 枚举类（支付状态、登录方式、用户状态、通知类型等常量）
    - `service/` - 服务类
      - `ConfigService` - 配置读写
      - `FileService` - 文件URL处理
      - `UploadService` - 文件上传
      - `pay/` - 支付（微信、支付宝）
      - `sms/` - 短信（阿里云、腾讯云）
      - `storage/` - 存储（本地、OSS、COS、七牛）
      - `wechat/` - 微信（小程序、公众号）
      - `generator/` - 代码生成器
    - `cache/` - 缓存（Token、权限）
    - `lists/` - 分页列表（搜索、排序、导出）
    - `logic/` - 公共逻辑（账户日志、通知、支付、退款）
    - `command/` - 命令行（定时任务、退款查询）
    - `controller/BaseLikeAdminController.php` - 控制器基类
- `config/`: 配置文件
- `public/`: Web 根目录（入口文件 `index.php`）
- `.env`: 环境变量（数据库、调试）

### Admin 目录结构 (`admin/src/`)
- `api/`: API 请求定义
- `views/`: 页面组件
- `components/`: 全局可复用组件
- `layout/`: 主布局结构
- `router/`: 路由定义
- `stores/`: Pinia 状态管理
- `utils/`: 工具函数

### Uniapp 目录结构 (`uniapp/src/`)
- `pages/`: 页面视图
- `api/`: API 请求定义
- `components/`: 可复用组件
- `uni_modules/`: 插件（如 vk-uview-ui）
- `manifest.json`: 应用配置
- `pages.json`: 页面路由和窗口配置

## 4. 开发规范

### Server（ThinkPHP）
- **架构**: Controller -> Logic -> Service -> Model
  - **Controller（控制器）**: 处理 HTTP 请求，参数验证。
  - **Logic（逻辑层）**: 业务逻辑实现。
  - **Service（服务层）**: 可复用服务（如上传、短信）。
  - **Model（模型）**: 数据库交互。
- **路由**: 定义在 `app/adminapi/config/route.php` 和 `app/api/config/route.php`。
- **响应格式**: 标准 JSON 结构（`code`, `msg`, `data`, `show`）。
- **认证**: 基于令牌的认证（AdminToken, UserToken）。

### Admin（Vue 3）
- **组件风格**: Composition API（`<script setup lang="ts">`）。
- **命名**: 组件使用 PascalCase，文件使用 kebab-case。
- **API 调用**: 定义在 `src/api/` 并在组件中导入。

### Uniapp
- **多平台**: 使用条件编译（`#ifdef`, `#endif`）处理平台特定逻辑。
- **样式**: SCSS，支持原子类（类 Tailwind）。

## 5. 配置与环境

### Server
- 复制 `.example.env` 为 `.env`
- 配置 `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`

### 前端（Admin/Uniapp/PC）
- `.env.development`: 开发环境变量
- `.env.production`: 生产环境变量
- 关键变量: `VITE_APP_BASE_URL`（API 基础地址）

## 6. 部署指南（简要）

### Nginx 配置
- 将 root 指向 `server/public`
- 启用 URL 重写（隐藏 `index.php`）
- 将 PHP 请求代理到 PHP-FPM（如 `127.0.0.1:9000`）

### 构建命令
- **Admin**: `npm run build`（输出: `server/public/admin`）
- **Uniapp (H5)**: `npm run build:h5`
- **PC**: `npm run build`

## 7. 编码规范与最佳实践

### 后端（ThinkPHP）
- **控制器继承**: 所有管理后台 API 控制器**必须**继承 `app\adminapi\controller\BaseAdminController`。
- **逻辑层**: 业务逻辑应放在 `Logic` 类中（如 `ConfigLogic`），**不要**放在控制器中。
- **服务使用**: 使用 `ConfigService::get('group', 'name')` 获取配置，使用 `FileService::getFileUrl($path)` 获取文件 URL。
- **模型 Trait**: 如需要安全删除，使用 `SoftDelete`。
- **命名规范**:
  - 控制器: `PascalCase`（如 `UserController`）
  - 数据表: `snake_case`（如 `ls_user`）
  - API 响应: 始终使用 `success($msg, $data)` 或 `fail($msg)` 方法（来自 `BaseLikeAdminController`）。
- **中间件**: 定义在 `app/adminapi/config/route.php`。包括 `InitMiddleware`、`LoginMiddleware`（认证）和 `AuthMiddleware`（权限）。
- **验证**: 使用 `app\adminapi\validate\BaseValidate` 子类。定义场景（如 `sceneDetail`）和自定义检查方法。
- **枚举**: 大量使用枚举（如 `NoticeEnum`、`UserEnum`），位于 `app/common/enum`。始终使用枚举而非魔法数字。

### 前端（Vue 3 Admin）
- **请求封装**: 使用 `@/utils/request` 中的 `request.get/post`。
  - 示例: `request.get({ url: '/path', params: {} })`
- **全局组件**:
  - `<material-picker>`: 用于选择图片/文件。
  - `<editor>`: 用于富文本编辑。
  - `<dict-value>`: 用于显示字典值。
  - `<daterange-picker>`: 用于日期范围选择。
  - `<upload>`: 用于文件上传（使用 `<el-upload>`）。
- **状态管理**: 使用 `@/stores` 中的 Pinia stores。
- **路由**: 在 `@/router` 中定义路由。
- **图标**: 使用 `src/assets/icons` 中的 SVG 图标或 Element Plus 图标。

### 小程序端（UniApp）
- **请求封装**: 使用 `@/utils/request` 中的 `request`。
  - 示例: `request.get({ url: '/path' }, { isAuth: true })`
- **配置**: 全局配置在 `src/config/index.ts`（baseUrl 等）。
- **单位**: 采用rpx（750宽屏）radius:29rpx
- **颜色主题**: 
 - 黑色1 #222929
 - 黑色2 #616B6B
 - 黑色3 #9CA6A6
 - 黑色4 #DADADA
 - 背景色 #F6F6F6
 - 绿文字 #00A2A0
 - 绿按钮 #00B6B4
 - 绿背景 #F3FAFA
 - 橙色 #FF7E22
 - 红色 #F04530


## 8. 常用逻辑模式

### 配置获取
- **后端**: `ConfigService::get('website', 'name')`
- **前端**: API `/config/getConfig` 返回全局配置。
- **Admin**: `useAppStore().config`
- **UniApp**: `@/config` 中的 `appConfig`

### 图片/文件处理
- **后端**: `FileService::getFileUrl($path)` 自动拼接域名。
- **Admin**: 使用 `<upload type="image" />` 上传，使用 `<material-picker v-model="form.image" />` 选择。
- **UniApp**: 使用 `uni.uploadFile` 或 uView 的 `<u-upload>`。

### 认证
- **Admin**: `useUserStore().token` 存储 JWT 令牌。
- **UniApp**: `@/utils/auth` 中的 `getToken()`。