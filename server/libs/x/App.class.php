<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 10:52
 */

namespace x;
use Smarty;
use x\Utils\ArrayUtil;
const VERSION = '0.1';

class App
{
    static private $_config;

    /**
     * App constructor.
     * @param null $moduleName 模块名称，指定了就会绑定，不指定系统自动根据路由判断
     */
    function __construct($moduleName = null){
        if(!defined('WEB_ROOT')
            ||!defined('APP_ROOT')
            ||!defined('APP_LIBS')
            ||!defined('APP_SRC')
            ||!defined('ENTRY_FILE')){
            echo "入口文件没有定义常量，请参考文档检查入口文件";
            return;
        }

        $config = self::getConfig();
        $this->run($moduleName);
	    header("Content-type:text/html;charset=".$config["charset"]);
//        $smarty = new Smarty();
//
//        $smarty->setTemplateDir(APP_TEMP_PATH.'app'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR);
//        $smarty->setCompileDir(APP_TEMP_PATH.'app'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR);
//        $smarty->setConfigDir(APP_TEMP_PATH.'app'.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR);
//        $smarty->setCacheDir(APP_TEMP_PATH.'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
//
//        $smarty->assign('title', $config['appTitle']);
//        $smarty->assign('version', "x(".VERSION.")");
//
//        $smarty->display('index.tpl');
        print_r($config['appTitle']."\n");
        print_r("x(".VERSION.")");

        //var_dump($config);
    }

    protected function run($moduleName = null){
        //$url ="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $arr = $this->getRouter();
        if($arr){
            if(!$moduleName){
                $moduleName = $arr[0];
                array_splice($arr,0,1);
            }
            $this->runModule($moduleName, $arr);
        }
    }
    protected function getRouter(){
        $url = $_SERVER['PHP_SELF'];
        $config = self::getConfig();
        switch ($config['router']['mode']){
            case 1:
                $arrUrl = explode('/',$url);
                return array_values(array_filter($arrUrl, function($item){
                    return !empty($item) && strlen($item) > 0 && strcasecmp($item, ENTRY_FILE)!=0;
                }));
                break;
        }

    }
    protected function runModule($moduleName, array $routePath){
        if(strcasecmp($moduleName, 'api') == 0){
            new Api\Handler($routePath);
            return;
        }
        new Mvc\Handler($moduleName, $routePath);
    }
    public static function getConfig(){
        if(self::$_config)return self::$_config;

        self::$_config = include_once APP_ROOT.'config'.DIRECTORY_SEPARATOR.'config.default.php';
        $configPath = APP_ROOT.'config'.DIRECTORY_SEPARATOR.'config.php';
        if(file_exists($configPath)){
            $config = include_once $configPath;
            self::$_config = ArrayUtil::merge(self::$_config, $config);
        }
        return self::$_config;
    }
}