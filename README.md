# x 框架

## 项目结构

``` bash
\-
 |-clinet                    #客户端程序
 |-public                    #网站公开目录（站点根目录）
   |-index.php               #网站入口文件
 |-server                    #服务端程序
   |-libs                    #库文件
     |-x                     #本框架
       |-App.class.php       #框架入口类（主类）
     |-CLoader.class.php     #加载器
   |-src                     #服务端程序
   |-vendor                  #composer管理的第三方库
 |-README.md                 #本文件
```

## composer
不知道这东西的同学请移步 [composer 中文][1]。

本框架使用了以下第三方库

* 缓存处理：[scrapbook][11]

``` bash
# 在server目录下
composer install
# 或者
composer.cmd install # windows CMD

```

## 入口文件

可以有多个入口文件

``` php
<?php
    define('WEB_ROOT',__DIR__.DIRECTORY_SEPARATOR);
    define('APP_ROOT', realpath(WEB_ROOT.'..'.DIRECTORY_SEPARATOR.'server').DIRECTORY_SEPARATOR);
    define('APP_LIBS', APP_ROOT.'libs'.DIRECTORY_SEPARATOR);
    define('APP_SRC', APP_ROOT.'src'.DIRECTORY_SEPARATOR);
    $arr = explode(DIRECTORY_SEPARATOR,__FILE__);
    define('ENTRY_FILE',array_pop($arr));
    
    require_once APP_LIBS.'CLoader.class.php';
    //绑定了Api模块
    //new x\App('Api');
    //没有绑定模块，系统通过路由自动判断模块
    new x\App();
```

## Api

在src目录下安装标准的命名空间方式创建 api类（继承`x\Api\BaseApi`）。
例如在src根目录下创建`test.php`。
``` php
<?php
    use x\Api\BaseApi;
    class test extends BaseApi{
        function getAction(){
                return "test";
            }
        function execute(){
            $this->success('这是测试api');
        }
    }
```
则可以通过`http://host/index.php/api/test`来调用此api

## 附录

* [composer 中文][1]
* [Smarty 中文文档][2]

[1]:http://www.phpcomposer.com/ (Composer 中文)
[2]:http://www.smarty.net/docs/zh_CN/ (Smarty 中文文档)
[11]:http://www.scrapbook.cash/ (缓存处理)
