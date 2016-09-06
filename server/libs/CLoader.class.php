<?php

function file_exists_case($filename) {
    if (is_file($filename)) {
        if (strstr(PHP_OS, 'WIN')) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 10:57
 */
class CLoader{
    public static function init(){
        if(false === spl_autoload_functions()){
            if(function_exists('__autoload')){
                spl_autoload_registe('__autoload',false);
            }
        }
        spl_autoload_register('CLoader::loader');
        require_once APP_ROOT."vendor".DIRECTORY_SEPARATOR."autoload.php";
    }
    public static function getClassName($className){
        if(strstr(PHP_OS, 'WIN')) {
            return "$className.class.php";
        }else{
            return str_replace("\\",DIRECTORY_SEPARATOR, "$className.class.php");
        }
    }
    public static function findFile($file, $noError = false){
        //首先查找库目录
        if(file_exists_case(APP_LIBS.$file)){
            return APP_LIBS.$file;
        }
        //加上命名空间“x”查找库目录
        if(file_exists_case(APP_LIBS."x".DIRECTORY_SEPARATOR.$file)){
            return APP_LIBS."x".DIRECTORY_SEPARATOR.$file;
        }
        //然后查找源代码目录
        if(file_exists_case(APP_SRC.$file)){
            return APP_SRC.$file;
        }
        if($noError)
            return false;
        throw new ErrorException('找不到：'.$file);
    }
    public static function loader($class_name){
        if(//spl_autoload_call($class_name) &&
        class_exists($class_name,false)) {
            return;
        }
        $path = self::findFile(self::getClassName($class_name));
        require_once $path;
    }
}
CLoader::init();
