<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 13:37
 */

namespace x\Api;


class Result
{
    public $message;
    public $code;
    public $debug;
    public $response_data;

    function __construct($msg,$code=-1){
        $this->message = $msg;
        $this->code = $code;
        $this->debug = getallheaders();
    }
}