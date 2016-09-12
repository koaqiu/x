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

/**
 * 获取访问TOKEN
 * @package x\Core
 */
class GetToken extends BaseApi
{

    function getAction()
    {
        return "Core.GetToken";
    }
	function getRequestType(){
		return "x\\Core\\GetTokenRequest";
	}
	function getResultType() {
		return "x\\Core\\GetTokenResult";
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

/**
 * Class GetTokenRequest
 * @package x\Core
 */
class GetTokenRequest{
	/**
	 * appid
	 * @var string
	 */
	public $id;

	/**
	 * app密钥
	 * @var string
	 */
	public $secret;
}

/**
 * Class GetTokenResult
 * @package x\Core
 */
class GetTokenResult{
	/**
	 * Token
	 * @var string
	 */
	public $token;

	/**
	 * 超时时间（秒）
	 * @var int 1800
	 */
	public $timeout;
}