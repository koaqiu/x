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

/**
 * x 类自动加载器
 * Class CLoader
 */
class CLoader{
    const CACHE_PATH = APP_TEMP_PATH."class_cache.php";
    protected static $_cache = null;

    /**
     * 注册加载器
     */
    public static function init(){
        if(false === spl_autoload_functions()){
            if(function_exists('__autoload')){
                spl_autoload_registe('__autoload',false);
            }
        }
        spl_autoload_register('CLoader::loader');
        require_once APP_ROOT."vendor".DIRECTORY_SEPARATOR."autoload.php";
    }

    /**
     * 从类名到文件名转换
     * @param $className
     * @return mixed|string
     */
    public static function getClassName($className){
        if(strstr(PHP_OS, 'WIN')) {
            return "$className.class.php";
        }else{
            return str_replace("\\",DIRECTORY_SEPARATOR, "$className.class.php");
        }
    }

    /**
     * 查找类文件是否存在，自动缓存
     * @param $file
     * @param bool $noError
     * @return bool|mixed|string
     * @throws ErrorException
     */
    public static function findFile($file, $noError = false){
        $cache = self::getCache();
        $md5 = md5($file);
        if(array_key_exists($md5, $cache)){
            return $cache[$md5];
        }
        //首先查找库目录
        if(file_exists_case(APP_LIBS.$file)){
            $cache[$md5] = APP_LIBS.$file;
            self::saveCache($cache);
            return APP_LIBS.$file;
        }
        //加上命名空间“x”查找库目录
        if(file_exists_case(APP_LIBS."x".DIRECTORY_SEPARATOR.$file)){
            $cache[$md5] = APP_LIBS."x".DIRECTORY_SEPARATOR.$file;
            self::saveCache($cache);
            return APP_LIBS."x".DIRECTORY_SEPARATOR.$file;
        }
        //然后查找源代码目录
        if(file_exists_case(APP_SRC.$file)){
            $cache[$md5] = APP_SRC.$file;
            self::saveCache($cache);
            return APP_SRC.$file;
        }
        if($noError)
            return false;
        throw new ErrorException('找不到：'.$file);
    }
    protected static function getCache(){
        if(self::$_cache)return self::$_cache;
        if(file_exists_case(self::CACHE_PATH)){
            self::$_cache = include APP_TEMP_PATH."class_cache.php";
        }else{
            self::$_cache = array();
        }
        return self::$_cache;
    }
    protected static function saveCache(array $config){
        file_put_contents(self::CACHE_PATH, "<?php \r return ".var_export($config, true).";");
    }

    /**
     * 加载器人口
     * @param $class_name
     */
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
