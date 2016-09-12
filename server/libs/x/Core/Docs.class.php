<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/10
 * Time: 17:44
 */

namespace x\Core;


use ReflectionProperty;
use x\Api\BaseApi;

/**
 * 获取文档信息，用于自动化生成说明文档或者代码等操作
 * @package x\Core
 */
class Docs extends BaseApi {
	/**
	 * 无效的API
	 */
	const ERROR_API_INVALID = 10404;

	function getAction() {
		return "Core.Docs";
	}

	function getRequestType() {
		return "x\\Core\\DocsRequest";
	}

	function getResultType() {
		return "x\\Api\\Result";
	}

	protected function findAction($action){
		$action = str_replace(".", "\\", $action);
		if (\CLoader::findFile(\CLoader::getClassName($action), true)) {
			if (class_exists($action)) {
				return $action;
			}elseif(class_exists("x\\".$action)){
				$tmp ="x\\".$action;
				return $tmp;
			}
		}
		return null;
	}
	function execute() {
		$request = $this->getInputParams();
		$apiName = $request->get("api", "Core.GetToken");
		$realApiName = $this->findAction($apiName);
		if($realApiName == null){
			return $this->error("API 错误 $realApiName",self::ERROR_API_INVALID);
		}
		$r = new \ReflectionClass($realApiName);
		if ($r) {
			if (is_subclass_of($realApiName, "x\\Api\\BaseApi")) {
				$result["name"] = $apiName;
				$this->dtdClassDoc($this->handlerComment($r->getDocComment()), $result);
				$api = new $realApiName(true);
				$constants = $r->getConstants();
				foreach ($constants as $key => $value){
					if(preg_match("/^ERROR_(.+)/S",$key, $matches)){
						$result["errorCode"][$matches[1]]=$value;
					}
				}
				$result["request"] = $this->dtdClass($api->getRequestType());
				$result["result"] = $this->dtdClass($api->getResultType());

				return $this->success($result);
			}
		}
		return $this->error("API 错误 $realApiName", self::ERROR_API_INVALID);
	}

	protected function dtdClassDoc($comment, &$data) {
		$lines = explode("\r", $comment);
		$note = '';
		$lines = array_map(function ($line) use (&$note, &$data) {
			if (preg_match("/^@(.+) {1,}(.+)/S", $line, $matches)) {
				$data[$matches[1]] = $matches[2];
				return null;
			} else {
				return $line;
			}
		}, $lines);
		$data['note'] = join("\r", array_filter($lines, function ($item) {
			return $item != null;
		}));
	}

	protected function dtdVarDoc($comment, &$data) {
		$lines = explode("\r", $comment);
		$note = '';
		$lines = array_map(function ($line) use (&$note, &$data) {
			if (preg_match("/^@(.+?) {1,}(.+)/S", $line, $matches)) {
				switch ($matches[1]) {
					case "var":
						$data['type'] = $matches[2];
						if (strpos($matches[2], " ") !== false) {
							$a2 = explode(" ", $matches[2]);
							$data['type'] = $a2[0];
							$data['defaultValue'] = join(" ", array_slice($a2, 1));
						}
						break;
					case "optional":
						$data['optional'] = true;
						break;
					default:
						$data[$matches[1]] = $matches[2];
						break;
				}
				return null;
			} else {
				return $line;
			}
		}, $lines);
		$data['note'] = join("\r", array_filter($lines, function ($item) {
			return $item != null;
		}));
	}

	protected function dtdClass($className) {
		if(empty($className) || $className == null || $className == false)
			return null;
		if(is_array($className)){
			return array(
				'name'=>"",
				'package'=>"",
				'note'=>"",
				'properties'=>$className,
			);
		}
		$r = new \ReflectionClass($className);
		if ($r) {
			$result["name"] = $r->getName();
			$this->dtdClassDoc($this->handlerComment($r->getDocComment()), $result);
			$properties = $r->getProperties(ReflectionProperty::IS_PUBLIC);
			$result["properties"] = array_map(function (ReflectionProperty $pro) {
				$data["name"] = $pro->getName();
				$doc = $this->handlerComment($pro->getDocComment());
				$this->dtdVarDoc($doc, $data);
				return $data;
			}, $properties);
			return $result;
		}
		return null;
	}//end function

	protected function handlerComment($str) {
		return join("\r", array_filter(array_map(function ($str) {
			return trim(preg_replace("/\\/{0,1}\\*{1,}\\/{0,1}/S", "", trim($str)));
		}, explode("\r", $str)), function ($str) {
			return strlen($str) > 0;
		}));
	}
}//end class

/**
 * Class DocsRequest
 * @package x\Core
 */
class DocsRequest {
	/**
	 * 要获取信息的API名称（示例：Core.Docs）
	 * @var string
	 * @optional true
	 */
	public $api;
}