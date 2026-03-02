---
name: figma-to-uniapp
description: figma to uniapp
---

# Figma to UniApp 页面转换

将 Figma/蓝湖 导出的页面转换为 UniApp 标准页面。


## 转换规则

### 1. 目录编号映射

根据 figma 目录名中的文件名自动推断位置

### 2. 单位转换

设计稿宽度 **412px** 对应 UniApp **750rpx**
- 转换比例：**1px ≈ 1.82rpx**

### 3. 标签转换

| 源标签 | 目标标签 |
|--------|----------|
| div | view |
| span | text |
| img | image |

### 4. 状态栏删除

自动删除包含状态栏（时间、电量、信号）和导航栏的 DOM 区域。
标题部分也是采用元素小程序，请忽略标题部分的原型代码。

### 5. 样式简化

- 删除 `font-family`、`overflow-wrap` 等冗余属性
- `rgba(x,x,x,1)` → `#十六进制`
- 负 margin 视情况调整
- UniApp 中 input 的 placeholder 使用 placeholder-style 

### 6. 图片处理

- 复制到目标页面 `assets/img/` 目录
- 根据用途重命名：
  - `icon_close.png` / `icon_back.png` - 图标
  - `bg_xxx.png` - 背景图
  - `illust_xxx.png` - 插图
  - `img_1.png` - 其他

## 输出结构

```
uniapp/src/pages/{target}/
├── {target}.vue
└── assets/
    ├── {target}.scss
    └── img/
        └── xxx.png
```

同时自动更新 `pages.json` 添加页面配置。

## 注意事项

1. 转换后的页面为**纯静态页面**，不含数据绑定和交互逻辑
2. 图片命名可能需要人工确认
3. 样式要放在vue文件的style标签内，且使用scoped属性
4. 类名等要重新设计，不能照搬设计稿中的命名
5. 仔细分析设计稿结构，不能原样照搬
6. 不必参考过多其他页面，大部分为框架自带的页面，和当前项目无关
7. 遇到不清楚的设计元素时，必须使用ask工具进行确认