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
    abstract function getAction();
    abstract function execute();
    function __construct(){
        Handler::Register($this);
    }

    protected function getInputParams(){
        return new Data(json_decode($this->getContent(), true));
    }
    protected function success($data){
        $result = new Result("", 0);
        $result->response_data = $data;
        return $result;
    }
    protected function error($message, $code = -1, $extData = null)
    {
        $result = new Result($message, $code);
        if($extData)
            $result-> extData = $extData;
        return $result;
    }
}