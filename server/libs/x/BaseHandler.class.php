<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 14:20
 */

namespace x;


/**
 * 客户端请求的处理程序基类
 * @package x
 */
class BaseHandler
{
    /**
     * GET请求
     */
    const METHOD_GET = 1;
    /**
     * POS请求
     */
    const METHOD_POST = 2;

    /**
     * 从客户端传第来的数据中读取有效数据
     * @param string $key           值名称
     * @param null|mixed $dv        默认值
     * @param int $method           请求类型
     * @return null|mixed
     */
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
     * @return false|string
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
     * @param null|mixed $dv
     * @param int $method
     * @return false|null|mixed
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

    public static function notFound(){
	    header("HTTP/1.1 404 Not Found");
	    header("Status: 404 Not Found");
	    echo "404";
	    exit(404);
	    return false;
    }
	public static function noPermission(){
	    header("HTTP/1.1 403 Not Permission");
	    header("Status: 403 Not Permission");
	    echo '403 Not Permission';
	    exit(403);
	    return false;
    }
	public static function redirect($url, $exit_code = 0, $timeout = 0){
    	if($timeout > 0){
    		echo '<html><head><meta http-equiv="refresh" content="'.$timeout.';url'.$url.'"></head><body>redirect : '.$url.'</body></html>';
	    }else {
		    header("HTTP/1.1 303 See Other");
		    header("Location: $url");
	    }
	    exit($exit_code);
	    return false;
    }
    /**
     * 判断是否Ajax请求
     * @return bool
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