<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 11:58
 */

namespace x\Api;


use x\App;
use x\BaseHandler;
use x\Utils\Cache;

class Handler extends BaseHandler
{
    static $_inc;

    function __construct(array $routePath)
    {
        self::$_inc = $this;
        $this->setAjaxHeader();
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->out(new Result("hello", 0));
            return;
        }
        $this->getAction($routePath);
        $this->error("没找到任何API");
    }

    protected function getAction(array $routePath)
    {
        if(count($routePath) < 1)
            return;
        $action = $routePath[0];
        if ($action !== false) {
            if(strcmp($action, 'Core.GetToken')){
                $this->checkToken();
            }
            $action = str_replace(".", "\\", $action);
            if (\CLoader::findFile(\CLoader::getClassName($action), true)) {
                if (class_exists($action)) {
                    return new $action;
                }elseif(class_exists("x\\".$action)){
                    $tmp ="x\\".$action;
                    return new $tmp;
                }
            }
        }
        return false;
    }
    protected function checkToken(){
        $config = App::getConfig()["api"];
        if($config && isset($config["secure"]) && $config["secure"] === false)
            return true;
        $token = $this->get('token');
        if($token){
            $value = Cache::getService()->get($token);
            if($value){
                return true;
            }
        }
        $this->error("TOKEN错误！", 4001);
        //$this->error($value, 4001);
        return false;
    }

    public static function Register(BaseApi $api)
    {
    	self::cacheDocs($api);
        try {
            self::$_inc->out($api->execute());
        } catch (\Exception $err) {
            self::$_inc->error($err->getMessage(), $err->getCode());
        } catch (\ErrorException $err) {
            self::$_inc->error($err->getMessage(), $err->getCode());
        }
    }
	private static function cacheDocs($api){
		$cachePath = APP_TEMP_PATH."api_cache.php";
		if(file_exists_case($cachePath)){
			$apiList = include $cachePath;
		}else{
			$apiList = array();
		}
		$r = new \ReflectionClass($api);
		if(array_key_exists($r->getName(), $apiList))return;
		$doc =  self::handlerComment($r->getDocComment());
		$data['name'] = str_replace("x.","",str_replace("\\",".",$r->getName()));
		self::dtdClassDoc($doc, $data);
		$apiList[$r->getName()]=$data;
		file_put_contents($cachePath, "<?php \r return ".var_export($apiList, true).";");
	}
	static function handlerComment($str) {
		return join("\r", array_filter(array_map(function ($str) {
			return trim(preg_replace("/\\/{0,1}\\*{1,}\\/{0,1}/S", "", trim($str)));
		}, explode("\r", $str)), function ($str) {
			return strlen($str) > 0;
		}));
	}
	static function dtdClassDoc($comment, &$data) {
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
    protected function setAjaxHeader()
    {
        header("Access-Control-Allow-Headers:Origin,X-Requested-With,X_Requested_With,Content-Type,Accept,Access-Control-Request-Method,X_FILENAME");
        header("Access-Control-Allow-Methods:GET,PUT,POST,HEAD,DELETE,OPTIONS");
        header("Access-Control-Max-Age:360000");
        header("Access-Control-Allow-Credentials:true");
        header("Access-Control-Allow-Origin:*");
    }

    protected function out(Result $data = null)
    {
    	$config = App::getConfig();
        $cb = $this->getOne(array('callback', '__cb'));
        if ($cb) {
            if ($data)
                $data->callback = $cb;
            header('Content-Type:application/json; charset='.$config["charset"]);
            exit($cb . '(' . json_encode($data, JSON_NUMERIC_CHECK) . ');');
        } else {
            header('Content-Type:application/json; charset='.$config["charset"]);
            exit(json_encode($data, JSON_NUMERIC_CHECK));
        }
    }

    protected function success($data)
    {
        $result = new Result("", 0);
        $result->response_data = $data;
        $this->out($result);
    }

    protected function error($message, $code = -1)
    {
        $result = new Result($message, $code);
        $this->out($result);
    }

}