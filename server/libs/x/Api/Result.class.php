<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 13:37
 */

namespace x\Api;

/**
 * API返回数据标准格式
 * @package x\Api
 */
class Result
{
	/**
	 * 错误信息
	 * @var string
	 */
    public $message;
	/**
	 * 错误代码，0-无错误
	 * @var int
	 */
    public $code;

	/**
	 * @var array|false
	 */
    public $debug;

	/**
	 * API正确执行会在这里把数据返回
	 * @var mixed
	 */
    public $response_data;

	/**
	 * Result constructor.
	 * @param     $msg
	 * @param int $code
	 */
    function __construct($msg,$code=-1){
        $this->message = $msg;
        $this->code = $code;
        $this->debug = getallheaders();
    }
}