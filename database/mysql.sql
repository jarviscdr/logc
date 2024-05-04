/*
 Navicat Premium Data Transfer

 Source Server         : A14
 Source Server Type    : MySQL
 Source Server Version : 80200 (8.2.0)
 Source Host           : 10.2.3.10:3306
 Source Schema         : logc

 Target Server Type    : MySQL
 Target Server Version : 80200 (8.2.0)
 File Encoding         : 65001

 Date: 27/04/2024 14:41:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for logs
-- ----------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `project` int NOT NULL DEFAULT 0 COMMENT '项目ID',
  `type` smallint NOT NULL DEFAULT 1 COMMENT '类型',
  `tags` json NULL COMMENT '标签',
  `content` json NULL COMMENT '内容',
  `content_text` text GENERATED ALWAYS AS (json_unquote(json_extract(`content`,_utf8mb4'$.content'))) STORED COMMENT '内容全文索引' NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日志时间',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_project`(`project`) USING BTREE,
  INDEX `idx_type`(`type`) USING BTREE,
  FULLTEXT INDEX `idx_search`(`content_text`)
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for projects
-- ----------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NULL DEFAULT NULL COMMENT '项目名称',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_name`(`name`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '项目表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(40) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `avatar` varchar(255) NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) NULL DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) NULL DEFAULT NULL COMMENT '手机',
  `created_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `login_at` datetime NULL DEFAULT NULL COMMENT '登录时间',
  `status` tinyint NULL DEFAULT NULL COMMENT '禁用',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
