# 需求模块 API 接口文档

## 概述

为 UniApp 小程序开发需求模块的后端 PHP 接口，支持用户发布需求、承接需求、确认完成等核心业务流程。

**技术栈**：ThinkPHP 8.0

**响应格式**：
```json
{
  "code": 1,      // 状态码：1成功，0失败
  "msg": "success",
  "data": {},     // 数据内容
  "show": 0       // 是否显示提示信息
}
```

**业务约束**：
- 微信支付暂不实现
- 取消/下架功能暂不实现
- 沟通功能暂不实现
- 承接需求需要先完成业主认证
- 陌生人只能查看"待接单"状态的需求详情

---

## 接口列表（共 11 个）

| 序号 | 接口 | 方法 | 需登录 | 说明 |
|------|------|------|--------|------|
| 1 | `/demand_category/lists` | GET | 否 | 需求分类列表 |
| 2 | `/demand/lists` | GET | 否 | 需求列表 |
| 3 | `/demand/detail` | GET | 否 | 需求详情 |
| 4 | `/demand/publish` | POST | 是 | 发布需求 |
| 5 | `/demand/edit` | POST | 是 | 编辑需求 |
| 6 | `/demand/accept` | POST | 是 | 承接需求 |
| 7 | `/demand/cancelAccept` | POST | 是 | 取消承接 |
| 8 | `/demand/finish` | POST | 是 | 确认完成 |
| 9 | `/demand/adjustAmount` | POST | 是 | 调整金额 |
| 10 | `/demand/myPublish` | GET | 是 | 我发布的需求 |
| 11 | `/demand/myAccept` | GET | 是 | 我承接的需求 |

---

## 接口详情

### 1. 需求分类列表

**接口**: `/demand_category/lists`
**方法**: GET
**需登录**: 否

**参数**: 无

**返回**:
```json
{
  "code": 1,
  "msg": "success",
  "data": [
    {
      "id": 1,
      "name": "轻需求",
      "icon": "https://xxx.com/icon.png",
      "sort": 100
    }
  ]
}
```

**备注**:
- 只返回 status=1 的启用分类
- 按 sort 升序排列

---

### 2. 需求列表

**接口**: `/demand/lists`
**方法**: GET
**需登录**: 否

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| category_id | int | 否 | 分类ID |
| community_id | int | 否 | 小区ID |
| status | int | 否 | 状态（默认1=待接单） |
| keyword | string | 否 | 标题关键词 |
| page_no | int | 否 | 页码，默认1 |
| page_size | int | 否 | 每页数量，默认10 |

**返回**:
```json
{
  "code": 1,
  "msg": "success",
  "data": {
    "list": [
      {
        "id": 1,
        "demand_no": "DQ20260310001",
        "title": "初三英语家教",
        "description": "需要找家教...",
        "images": ["https://xxx.com/1.jpg"],
        "amount": "160.00",
        "price_type": 1,
        "price_type_desc": "按小时",
        "hours": 2,
        "hour_price": "80.00",
        "min_amount": null,
        "max_amount": null,
        "community_name": "阳光小区",
        "address": "1栋101室",
        "is_urgent": 0,
        "status": 1,
        "status_desc": "待接单",
        "create_time": "2026-03-10 10:00:00",
        "user_info": {
          "id": 1,
          "nickname": "张三",
          "avatar": "https://xxx.com/avatar.jpg"
        },
        "category_name": "陪伴需求"
      }
    ],
    "count": 100,
    "page_no": 1,
    "page_size": 10
  }
}
```

**备注**:
- 默认只返回 status=1（待接单）的需求
- 列表中不展示联系方式（隐私保护）
- 按 create_time 降序排列

---

### 3. 需求详情

**接口**: `/demand/detail`
**方法**: GET
**需登录**: 否

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 需求ID |

**返回**:
```json
{
  "code": 1,
  "msg": "success",
  "data": {
    "id": 1,
    "demand_no": "DQ20260310001",
    "title": "初三英语家教",
    "description": "需要找家教...",
    "images": ["https://xxx.com/1.jpg"],
    "amount": "160.00",
    "price_type": 1,
    "price_type_desc": "按小时",
    "hours": 2,
    "hour_price": "80.00",
    "min_amount": null,
    "max_amount": null,
    "community_id": 1,
    "community_name": "阳光小区",
    "address": "1栋101室",
    "contact_name": "张先生",
    "contact_phone": "138****8888",
    "show_phone": 1,
    "is_urgent": 0,
    "service_rate": "0.00",
    "status": 1,
    "status_desc": "待接单",
    "accept_user_id": null,
    "accept_time": null,
    "final_amount": null,
    "service_fee": null,
    "create_time": "2026-03-10 10:00:00",
    "user_info": {
      "id": 1,
      "nickname": "张三",
      "avatar": "https://xxx.com/avatar.jpg"
    },
    "category": {
      "id": 5,
      "name": "陪伴需求"
    },
    "accept_info": null,
    "is_owner": false,
    "is_accepter": false,
    "can_accept": true
  }
}
```

**权限控制（后端）**:

| 角色 | 待接单 | 进行中 | 已完成 | 已取消 | 已下架 | 已结算 |
|------|--------|--------|--------|--------|--------|--------|
| 陌生人 | 可查看 | 拒绝访问 | 拒绝访问 | 拒绝访问 | 拒绝访问 | 拒绝访问 |
| 发布者 | 完整权限 | 完整权限 | 完整权限 | 完整权限 | 完整权限 | 完整权限 |
| 承接者 | 可承接 | 完整权限 | 完整权限 | 完整权限 | - | 完整权限 |

**说明**:
- 陌生人查看待接单时：contact_phone 根据 show_phone 决定是否脱敏
- 发布者/承接者：可查看完整联系电话
- `is_owner`: 当前用户是否为发布者
- `is_accepter`: 当前用户是否为承接者
- `can_accept`: 是否可以承接（需登录且已认证且状态为待接单）

---

### 4. 发布需求

**接口**: `/demand/publish`
**方法**: POST
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| category_id | int | 是 | 分类ID |
| title | string | 是 | 标题，最长50字 |
| description | string | 是 | 描述，最长500字 |
| images | array | 否 | 图片数组，最多9张 |
| price_type | int | 是 | 金额类型：1=按小时，2=按次，3=按范围 |
| hours | float | 条件 | 小时数（price_type=1必填） |
| hour_price | float | 条件 | 每小时单价（price_type=1必填） |
| amount | float | 条件 | 金额（price_type=2必填） |
| min_amount | float | 条件 | 最小金额（price_type=3必填） |
| max_amount | float | 条件 | 最大金额（price_type=3必填） |
| community_id | int | 是 | 小区ID（必填） |
| address | string | 是 | 详细地址（楼栋/房间号） |
| contact_name | string | 是 | 联系人姓名 |
| contact_phone | string | 是 | 联系电话 |
| show_phone | int | 否 | 是否展示电话：0=否，1=是，默认0 |
| is_urgent | int | 否 | 是否紧急：0=否，1=是，默认0 |

**返回**:
```json
{
  "code": 1,
  "msg": "发布成功",
  "data": {
    "id": 1,
    "demand_no": "DQ20260310001"
  },
  "show": 1
}
```

**业务规则**:
1. 自动生成需求编号：DQ + 年月日 + 4位序号（如 DQ20260310001）
2. 紧急发布（is_urgent=1）时，service_rate = 3.00（3%服务费）
3. 非紧急发布时，service_rate = 0
4. 根据 price_type 计算 amount：
   - 按小时：amount = hours × hour_price
   - 按次：amount = 传入值
   - 按范围：amount = min_amount
5. 初始状态为 1（待接单）

---

### 5. 编辑需求

**接口**: `/demand/edit`
**方法**: POST
**需登录**: 是

**参数**: 同发布需求，额外增加：

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| id | int | 是 | 需求ID |

**返回**:
```json
{
  "code": 1,
  "msg": "编辑成功",
  "data": [],
  "show": 1
}
```

**业务规则**:
1. 只有发布者可以编辑
2. 只有待接单状态（status=1）可以编辑
3. 重新计算 amount

---

### 6. 承接需求

**接口**: `/demand/accept`
**方法**: POST
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| demand_id | int | 是 | 需求ID |

**返回**:
```json
{
  "code": 1,
  "msg": "承接成功",
  "data": [],
  "show": 1
}
```

**业务规则**:
1. **前置条件检查**：
   - 用户必须已完成业主认证（status=1）
   - 需求状态必须为 1（待接单）
   - 不能承接自己发布的需求
2. 承接成功后：
   - 需求状态变更为 2（进行中）
   - 记录 accept_user_id 和 accept_time
   - 创建承接记录（ls_demand_accept）
3. 错误提示：
   - 未认证：`请先完成业主认证`
   - 状态不对：`该需求已被承接或已关闭`
   - 自己的需求：`不能承接自己发布的需求`

---

### 7. 取消承接

**接口**: `/demand/cancelAccept`
**方法**: POST
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| demand_id | int | 是 | 需求ID |

**返回**:
```json
{
  "code": 1,
  "msg": "取消成功",
  "data": [],
  "show": 1
}
```

**业务规则**:
1. 只有当前承接者可以取消
2. 需求状态必须为 2（进行中）
3. 取消后：
   - 需求状态变为 1（待接单）
   - 清空 accept_user_id 和 accept_time
   - 更新承接记录状态为 2（已取消）

---

### 8. 确认完成

**接口**: `/demand/finish`
**方法**: POST
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| demand_id | int | 是 | 需求ID |

**返回**:
```json
{
  "code": 1,
  "msg": "确认完成",
  "data": [],
  "show": 1
}
```

**业务规则**:
1. 只有发布者可以确认完成
2. 需求状态必须为 2（进行中）
3. 确认后：状态变为 3（已完成）

---

### 9. 调整金额

**接口**: `/demand/adjustAmount`
**方法**: POST
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| demand_id | int | 是 | 需求ID |
| amount | float | 是 | 新金额 |

**返回**:
```json
{
  "code": 1,
  "msg": "调整成功",
  "data": [],
  "show": 1
}
```

**业务规则**:
1. 只有发布者可以调整
2. 需求状态必须为 2（进行中）或 3（已完成）
3. 只更新 amount 字段

---

### 10. 我发布的需求

**接口**: `/demand/myPublish`
**方法**: GET
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| status | int | 否 | 状态筛选 |
| page_no | int | 否 | 页码，默认1 |
| page_size | int | 否 | 每页数量，默认10 |

**返回**:
```json
{
  "code": 1,
  "msg": "success",
  "data": {
    "list": [
      {
        "id": 1,
        "demand_no": "DQ20260310001",
        "title": "初三英语家教",
        "images": ["https://xxx.com/1.jpg"],
        "amount": "160.00",
        "price_type": 1,
        "price_type_desc": "按小时",
        "status": 2,
        "status_desc": "进行中",
        "is_urgent": 0,
        "create_time": "2026-03-10 10:00:00",
        "category_name": "陪伴需求",
        "accept_user": {
          "id": 2,
          "nickname": "李四",
          "avatar": "https://xxx.com/avatar.jpg",
          "mobile": "138****8888"
        },
        "accept_time": "2026-03-10 11:00:00"
      }
    ],
    "count": 10,
    "page_no": 1,
    "page_size": 10
  }
}
```

**备注**:
- 按 create_time 降序排列
- 返回当前用户发布的所有状态的需求

---

### 11. 我承接的需求

**接口**: `/demand/myAccept`
**方法**: GET
**需登录**: 是

**参数**:
| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| status | int | 否 | 状态筛选 |
| page_no | int | 否 | 页码，默认1 |
| page_size | int | 否 | 每页数量，默认10 |

**返回**:
```json
{
  "code": 1,
  "msg": "success",
  "data": {
    "list": [
      {
        "id": 1,
        "demand_no": "DQ20260310001",
        "title": "初三英语家教",
        "images": ["https://xxx.com/1.jpg"],
        "amount": "160.00",
        "price_type": 1,
        "price_type_desc": "按小时",
        "status": 2,
        "status_desc": "进行中",
        "is_urgent": 0,
        "create_time": "2026-03-10 10:00:00",
        "accept_time": "2026-03-10 11:00:00",
        "category_name": "陪伴需求",
        "community_name": "阳光小区",
        "address": "1栋101室",
        "contact_name": "张先生",
        "contact_phone": "13888888888",
        "publish_user": {
          "id": 1,
          "nickname": "张三",
          "avatar": "https://xxx.com/avatar.jpg"
        }
      }
    ],
    "count": 5,
    "page_no": 1,
    "page_size": 10
  }
}
```

**备注**:
- 按 accept_time 降序排列
- 返回当前用户承接的所有状态的需求
- 包含完整联系方式（承接者可见）

---

## 附录

### 状态枚举

| 值 | 名称 | 说明 |
|----|------|------|
| 1 | 待接单 | 需求已发布，等待承接 |
| 2 | 进行中 | 已被承接，正在进行 |
| 3 | 已完成 | 发布者确认完成，待结算 |
| 4 | 已取消 | 需求被取消 |
| 5 | 已下架 | 需求被下架 |
| 6 | 已结算 | 平台结算完成 |

### 金额类型枚举

| 值 | 名称 | 说明 |
|----|------|------|
| 1 | 按小时 | 金额 = 小时数 × 每小时单价 |
| 2 | 按次 | 固定金额 |
| 3 | 按范围 | 显示金额范围，amount 取 min_amount |

### 承接记录状态枚举

| 值 | 名称 | 说明 |
|----|------|------|
| 1 | 承接中 | 当前正在承接 |
| 2 | 已取消 | 承接者取消承接 |
| 3 | 已完成 | 需求已完成 |

### 业务流程

```
发布需求 → 待接单 → 承接需求 → 进行中 → 确认完成 → 已完成 → 平台结算 → 已结算
                                                    ↓
                                               取消承接（回到待接单）
```
