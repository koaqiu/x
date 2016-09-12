<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 11:18
 */

return array(
    "appTitle" => 'x 框架',
    "appVer" => '0.1',
    "debug" => true,
    "router" => array(
        /**
         * 模式：1-phpinfo
         */
        "mode" => 1
    ),
    "db" => array(
        "host" => "localhost",
        "port" => 3306,
        "user" => "root",
        "pass" => "",
        "dbName" => "x",
        "prefix"=>false,//表名前缀
    ),
    "cache" => array(
        "type" => 7,//1- Memcached, 2- Redis, 3-APC,4-MySql,5-SQLite,6-Memory
        "host" => "127.0.0.1",//Memcached、Redis、MySql需要
        "port" => 6379,//Memcached、Redis、MySql需要
        "user" => "root",//Memcached、Redis、MySql需要
        "pass" => "",//Memcached、Redis、MySql需要
        "dbName" => "x",//MySql需要
        "dbFile" => "",//SQLite 数据库的文件地址
        "memory_limit" => "100M",//Memory 内存限制
    ),
    "api"=>array(
        "secure"=>"TOKEN",//安全性：使用 token（默认），false 不进行验证
    ),
	"ShowDocs"=>true,
);