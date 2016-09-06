<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/6
 * Time: 9:55
 */

namespace x\Utils;


class xString
{

    public static function FromJson($str){
        if (!$str || !is_string($str) || strlen($str) < 1)
            return false;
        return json_decode($str);
    }
    public static function LeftTrim($str, $toRemove = " ")
    {
        if (!$str)
            return $str;
        return preg_replace("/^(" . $toRemove . ")+/S", "", $str);
    }

    public static function RightTrim($str, $toRemove = " ")
    {
        if (!$str)
            return $str;
        return preg_replace("/(" . $toRemove . ")+$/S", "", $str);
    }

    public static function Trim($str, $toRemove = " ")
    {
        if (!$str)
            return $str;
        return self::LeftTrim(self::RightTrim($str, $toRemove), $toRemove);
    }
}