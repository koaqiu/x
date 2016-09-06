<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 14:20
 */

namespace x;


class BaseHandler
{
    const METHOD_GET = 1;
    const METHOD_POST = 2;

    protected function get($key, $dv = null, $method = self::METHOD_GET)
    {
        switch ($method) {
            case self::METHOD_GET:
                if (isset($_GET[$key])) {
                    return $_GET[$key];
                }
                break;
            case self::METHOD_POST:
                if (isset($_POST[$key])) {
                    return $_POST[$key];
                }
                break;
        }
        return $dv;
    }

    /**
     * 读取原始POST数据
     * @return bool|mixed
     */
    protected function getContent(){
//        if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
//            return $GLOBALS['HTTP_RAW_POST_DATA'];
        try {
            $raw_post_data = file_get_contents('php://input', 'r');
            return $raw_post_data;
        }catch (\ErrorException $err){

        }
        return false;
    }
    /**
     * 传入一组key，按顺序读取参数，直到有有效值为止，如果都没有返回dv
     * @param array $keys
     * @param null $dv
     * @param int $method
     * @return bool|null
     */
    protected function getOne(array $keys, $dv = null, $method = self::METHOD_GET)
    {
        $value = false;
        array_map(function ($key) use (&$value, $method) {
            if ($value) return $key;
            switch ($method) {
                case self::METHOD_GET:
                    if (isset($_GET[$key])) {
                        $value = $_GET[$key];
                    }
                    break;
                case self::METHOD_POST:
                    if (isset($_POST[$key])) {
                        $value = $_POST[$key];
                    }
                    break;
            }
            return $key;
        }, $keys);
        if ($value)
            return $value;
        else
            return $dv;
    }

    /**
     * 判断是否Ajax请求
     */
    protected function isAjaxRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }
}