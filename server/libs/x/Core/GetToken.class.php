<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/4
 * Time: 0:49
 */

namespace x\Core;


use x\Api\BaseApi;
use x\Utils\Cache;

class GetToken extends BaseApi
{
    function getAction()
    {
        return "Core.GetToken";
    }

    function execute()
    {
        $data = $this->getInputParams();
        $id = $data->get('id');
        if (!$id) {
            return $this->error('没有指定：id');
        }
        $secret = $data->get('secret');
        if (!$secret) {
            return $this->error('没有指定：secret');
        }
        $token = md5("id:$id,secret:$secret");
        Cache::getService()->set($token, array(
            "id" => $id,
            "secret" => $secret
        ));
        return $this->success(array(
            "token" => $token,
            "timeout" => 1800,
            "test"=>Cache::getService()->get($token)

        ));
    }
}