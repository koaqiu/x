<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/5
 * Time: 10:09
 */

namespace x\Mvc;


use x\BaseHandler;

class Handler extends BaseHandler
{
    function __construct($moduleName, array $routePath)
    {
        $configPath = APP_SRC.$moduleName.DIRECTORY_SEPARATOR."config.php";
        if(!file_exists_case($configPath)){
            return;
        }
        define("APP_MODULE", $moduleName);

        $config = include_once $configPath;

        switch (count($routePath)){
            case 0:
                array_push($routePath, "Index", "Index");
                break;
            case 1:
                array_push($routePath, "Index");
                break;
        }
        $controller = $routePath[0];
        array_splice($routePath,0,1);
        $inc = $this->findController($controller);
        if(is_subclass_of($inc,"x\\Mvc\\BaseController")){
            $inc -> _init($routePath, $config);
            return;
        }
        var_dump($config);
        die(0);
    }
    protected function findController($controller){
        $controllerPath = APP_SRC.APP_MODULE.DIRECTORY_SEPARATOR."Controller".DIRECTORY_SEPARATOR.$controller.".class.php";
        if(!file_exists_case($controllerPath)){
            return false;
        }
        $controllerClass = APP_MODULE."\\Controller\\".$controller;
        return new $controllerClass;
    }
}