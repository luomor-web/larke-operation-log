## larke-admin 操作日志扩展


### 项目介绍

*  记录 admin 系统的相关操作日志


### 环境要求

 - PHP >= 8.0
 - Laravel >= 9.0.0
 - larke-admin >= 1.3.0


### 安装步骤

1、下载安装扩展

```php
composer require larke/operation-log
```

或者在`本地扩展->扩展管理->上传扩展` 本地上传

2、然后在 `本地扩展->扩展管理->安装/更新` 安装本扩展

3、安装后可以在 `public/extension/operation-log` 发现本扩展的前端文件

4、将 `operation-log` 该文件夹复制到前端编译目录 `src/extension` 下进行编译预览

5、你可以在 `src/routes.js` 文件修改扩展在左侧菜单的排序


### 开源协议

*  本软件 遵循 `Apache2` 开源协议发布，在保留本软件版权的情况下提供个人及商业免费使用。 


### 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
