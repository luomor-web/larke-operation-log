DROP TABLE IF EXISTS `pre__ext_operation_log`;
CREATE TABLE `pre__ext_operation_log` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '日志ID',
  `admin_id` char(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '管理账号ID',
  `admin_name` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '管理账号',
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '请求类型',
  `info` text CHARACTER SET utf8mb4 NOT NULL COMMENT '内容信息',
  `useragent` text CHARACTER SET utf8mb4 NOT NULL COMMENT 'User-Agent',
  `ip` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_ip` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '创建IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='操作日志';
