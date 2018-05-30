<?php

use Phpmig\Migration\Migration;

class Log extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $biz = $this->getContainer();
        $connection = $biz['db'];
        $connection->exec("
            CREATE TABLE `log` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统日志ID',
              `clientId` varchar(128) NOT NULL COMMENT '操作客户端ID',
              `module` varchar(32) NOT NULL COMMENT '日志所属模块',
              `action` varchar(32) NOT NULL COMMENT '日志所属操作类型',
              `message` text NOT NULL COMMENT '日志内容',
              `data` text DEFAULT NULL COMMENT '日志数据',
              `ip` varchar(255) NOT NULL COMMENT '日志记录IP',
              `createdTime` int(10) unsigned NOT NULL COMMENT '日志发生时间',
              `level` char(10) NOT NULL COMMENT '日志等级',
              PRIMARY KEY (`id`),
              KEY `clientId` (`clientId`)
            ) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $biz = $this->getContainer();
        $connection = $biz['db'];
        $connection->exec("
            DROP TABLE `log`;
        ");
    }
}
