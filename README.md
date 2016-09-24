# x 框架

## 项目结构

``` bash
\-
 |-clinet                    #客户端程序
 |-public                    #网站公开目录（站点根目录）
   |-index.php               #网站入口文件
 |-server                    #服务端程序
   |-config                  #保存配置文件
     |-config.defualt.php    #默认的配置信息
   |-libs                    #库文件
     |-x                     #本框架
       |-App.class.php       #框架入口类（主类）
     |-CLoader.class.php     #加载器
   |-src                     #服务端程序
   |-vendor                  #composer管理的第三方库
 |-temp                      #存储：临时文件、缓存数据  
 |-README.md                 #本文件
```

## 写在前面

**本框架大小写敏感**，如果使用过程中发生异常请第一时间检查是否搞错大小写了。

## 依赖

本框架使用了以下第三方库

### scrapbook

缓存处理，参考：[scrapbook][11]

### composer

不知道这东西的同学请移步 [composer 中文][1]。

``` bash
# 在server目录下
composer install
# 或者
composer.cmd install # windows CMD

```

### Smarty

本框架使用了 [Smarty][2] 作为模板解析引擎


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

在src目录下按照标准的命名空间方式创建 api类（继承`x\Api\BaseApi`）即可。
例如在src根目录下创建`test.class.php`。
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


## 安全

本框架提供了一个默认解决方案（配置文件已经定义好了），如果想使用自己的方案，请参考：

* 创建一个“会员资格”类，必须实现接口`x\Core\Secure\IMemberShip`
* 创建一个权限检查类，必须实现接口`x\Core\Secure\IPermission`
* 修改配置文件
    ``` php
    //配置文件片段
    "secure" => array(
        "enabled" => true,
        "handler" => "x\\Core\\Secure\\DefaultChecker",//权限检查处理程序，必须实现 x\Core\Secure\IPermission
        "memberShip" => "", //用户管理，必须实现 x\Core\Secure\IMemberShip，空表示使用系统默认
    }
    ```
* 搞定，系统会在执行页面代码之前根据Module、Controller、Action检查用户权限

可以修改配置文件完全禁用权限检查，这样你就必须自己额外编写代码实现权限控制

``` php
//配置文件片段
"secure" => array(
    "enabled" => false,//禁用权限控制
}
```

## 附录

* [composer 中文][1]
* [Smarty 中文文档][2]

[1]:http://www.phpcomposer.com/ (Composer 中文)
[2]:http://www.smarty.net/docs/zh_CN/ (Smarty 中文文档)
[11]:http://www.scrapbook.cash/ (缓存处理)
