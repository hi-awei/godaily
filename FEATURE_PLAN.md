# GoDaily 新功能开发计划

## 功能一：工具评论系统

### 数据库设计
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL,
    parent_id INT DEFAULT 0 COMMENT '回复的评论ID，0表示顶级评论',
    nickname VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    content TEXT NOT NULL,
    rating TINYINT DEFAULT 0 COMMENT '1-5星评分，0表示不评分',
    ip VARCHAR(45),
    status TINYINT DEFAULT 1 COMMENT '0=待审核 1=已发布 2=已删除',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tool (tool_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 前端页面
1. tool.php 添加评论区展示
2. 提交评论表单（昵称、邮箱、内容、评分）
3. CSS样式美化

### API接口
1. api.php?action=comment_add - 提交评论
2. api.php?action=comments&tool_id=X - 获取评论列表

---

## 功能二：用户收藏系统

### 数据库设计
```sql
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_id INT NOT NULL,
    user_hash VARCHAR(64) NOT NULL COMMENT '用户唯一标识（IP+UA哈希）',
    ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tool_user (tool_id, user_hash),
    INDEX idx_user (user_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 前端实现
1. tool.php 添加收藏按钮
2. 使用 localStorage 存储用户收藏状态
3. 收藏数显示

### API接口
1. api.php?action=favorite_add - 添加收藏
2. api.php?action=favorite_remove - 取消收藏
3. api.php?action=favorites&user_hash=X - 获取用户收藏列表

---

## 开发步骤

1. 创建数据库表
2. 修改 api.php 添加接口
3. 修改 tool.php 添加UI
4. 修改 style.css 添加样式
5. 修改 main.js 添加交互逻辑
6. 创建收藏页面 favorites.php
