<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/5
 * Time: 10:09
 */

namespace x\Mvc;


use x\App;
use x\BaseHandler;

class Handler extends BaseHandler {
	function __construct($moduleName, array $routePath) {
//        $configPath = APP_SRC.$moduleName.DIRECTORY_SEPARATOR."config.php";
//        if(!file_exists_case($configPath)){
//            return;
//        }
//        define("APP_MODULE", $moduleName);
		$configPath = false;
		$appConfig = App::getConfig();
		if(strcasecmp($moduleName, "Docs") != 0 || $appConfig["ShowDocs"] === true){
			$configPath = $this->getModuleConfig($moduleName, APP_LIBS . "x" . DIRECTORY_SEPARATOR);
		}

		if ($configPath === false) {
			$configPath = $this->getModuleConfig($moduleName, APP_SRC);
		}
		if ($configPath === false) {
			return;
		}
		define("APP_MODULE", $moduleName);
		$config = include_once $configPath;
		if(isset($config['defaultController'])){
			//配置了默认控制器，且没有指定完整的路径，自动使用默认控制器
			//默认控制器的Action不能和其他控制器重名，否则会覆盖掉
			if(count($routePath) < 2){
				array_unshift($routePath, $config['defaultController']);
			}
		}
		switch (count($routePath)) {
			case 0:
				array_push($routePath, "Index", "Index");
				break;
			case 1:
				array_push($routePath, "Index");
				break;
		}
		$controller = $routePath[0];
		array_splice($routePath, 0, 1);
		$inc = $this->findController($controller);
		if (is_subclass_of($inc, "x\\Mvc\\BaseController")) {
			$inc->_init($routePath, $config);
			return;
		}
		var_dump($config);
		die(0);
	}

	protected function getModuleConfig($moduleName, $basePath) {
		$configPath = $basePath . $moduleName . DIRECTORY_SEPARATOR . "config.php";
		if (file_exists_case($configPath)) {
			return $configPath;
		}
		return false;
	}

	protected function findController($controller) {
		$controllerPath = APP_LIBS . "x" . DIRECTORY_SEPARATOR . APP_MODULE . DIRECTORY_SEPARATOR . "Controller" . DIRECTORY_SEPARATOR . $controller . ".class.php";
		if (!file_exists_case($controllerPath)) {
			$controllerPath = APP_SRC . APP_MODULE . DIRECTORY_SEPARATOR . "Controller" . DIRECTORY_SEPARATOR . $controller . ".class.php";
			if (!file_exists_case($controllerPath)) {
				return false;
			}else{
				$controllerClass = APP_MODULE . "\\Controller\\" . $controller;
			}
		}else{
			$controllerClass = "x\\".APP_MODULE . "\\Controller\\" . $controller;
		}

		return new $controllerClass;
	}
}