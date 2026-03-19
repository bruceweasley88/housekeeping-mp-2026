-- 用户银行卡表
CREATE TABLE IF NOT EXISTS `ls_user_bankcard` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `bankcard_image` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡图片',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态: 0=待审核, 1=已通过, 2=已拒绝',
  `reject_reason` varchar(255) DEFAULT '' COMMENT '拒绝原因',
  `verified_at` datetime DEFAULT NULL COMMENT '审核时间',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户银行卡';
