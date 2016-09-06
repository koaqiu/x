<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 23:58
 */

namespace x\Utils;


class ArrayUtil
{

    /**
     * 递归合并数组
     * 如果传入的参数都不是数组，则返回一个由这些值组成的简单数组（数字索引）
     * 如果没有传值返回false
     * 如果传入的值有部分不是数组，会自动添加到最后
     * @return false|bool|mixed
     */
    public static function merge(){
        $argc_tmp = func_num_args();
        if($argc_tmp < 1)
            return false;
        $argv_tmp = func_get_args();
        $argv = array_filter($argv_tmp, function($item){return is_array($item) && count($item) > 0;});
        $argc = count($argv);
        if($argc == 0)
            return $argv_tmp;
        if($argc != $argc_tmp) {
            $no_array = array_filter($argv_tmp, function ($item) {
                return !is_array($item);
            });
            array_push($argv, $no_array);
        }
        $last = array_pop($argv);
        while (count($argv) > 0) {
            $then = array_pop($argv);
            foreach ($last as $key => $value){
                if(!array_key_exists($key, $then)){
                    $then[$key] = $value;
                    continue;
                }
                if(is_array($value)){
                    $then[$key] = self::merge($then[$key], $value);
                }else{
                    $then[$key] = $value;
                }
            }
            $last = $then;
        }
        return $last;
    }
}