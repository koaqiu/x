<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 15:03
 */

namespace x\Api;

use x\BaseHandler;
use x\Utils\Data;

/**
 * API方法的基类所有API都重这里继承
 * @package x\Api
 */
abstract class BaseApi extends BaseHandler
{
    /**
     * 返回API完整名称
     * @return string
     */
    abstract function getAction();

    /**
     * 执行API
     * @return Result
     */
    abstract function execute();
    function __construct(){
        Handler::Register($this);
    }

    /**
     * 获取API参数
     * @return Data
     */
    protected function getInputParams(){
        return new Data(json_decode($this->getContent(), true));
    }

    /**
     * API执行成功时调用
     * @param $data
     * @return Result
     */
    protected function success($data){
        $result = new Result("", 0);
        $result->response_data = $data;
        return $result;
    }

    /**
     * API执行失败时调用
     * @param string $message          错误信息
     * @param int $code                错误代码，默认-1
     * @param null $extData            扩展数据
     * @return Result
     */
    protected function error($message, $code = -1, $extData = null)
    {
        $result = new Result($message, $code);
        if($extData)
            $result-> extData = $extData;
        return $result;
    }
}