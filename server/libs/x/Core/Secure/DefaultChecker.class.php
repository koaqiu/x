<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/23
 * Time: 11:40
 */

namespace x\Core\Secure;


use x\App;
use x\Core\Factory;
use x\Db\Dbase;

class DefaultChecker implements IPermission {
	private $appConfig;
	private $config;
	private $db;
	function __construct() {
		$this->appConfig = App::getConfig();
		$this->config = $this->appConfig["secure"];
		$this->db = Dbase::get($this->config["db"]);
	}


	protected function getUserId(){
		$memberShip = Factory::getMemberShip($this->config);
		if($memberShip){
			return $memberShip->getUserId();
		}
		return 0;
	}
	private static $MODULE_WHITE_LIST = array(
		'Docs'
	);
	protected function checkWhiteList($module, $controller, $action){
		$white_list = $this->config['white_list'];
		if(count($white_list) == 0)
			return false;

		if(!isset($white_list[$module]))
			return false;

		$moduleConfig = $white_list[$module];
		if($moduleConfig === "*")
			return true;
		if(!is_array($moduleConfig) || count($moduleConfig) == 0)
			return false;

		if(!isset($moduleConfig[$controller]))
			return false;

		$controllerConfig = $moduleConfig[$controller];
		if($controllerConfig === "*")
			return true;
		if(!is_array($controllerConfig) || count($controllerConfig) == 0)
			return false;

		return array_search($action, $controllerConfig) >= 0;
	}
	/**
	 * 检查当前用户的权限，有权限返回true，否则返回false
	 * @param $module
	 * @param $controller
	 * @param $action
	 * @return boolean
	 */
	function check($module, $controller, $action){
		//var_dump(func_get_args());
		//检查内置的白名单
		$isFound = array_search($module, self::$MODULE_WHITE_LIST);
		if($isFound !== false){
			return true;
		}
		//检查配置的白名单
		if($this->checkWhiteList($module, $controller, $action)){
			return true;
		}
		$list = $this->getMyPermission($this->getUserId());
		if(count($list) == 0){
			return false;
		}
		$f = function($item) use($module, $controller, $action){
			if($item['p_module'] == "*")
				return true;
			if($module == $item['p_module']){
				if($item['p_controller'] == "*")
					return true;
				if($controller == $item['p_controller']) {
					if ($item['p_action'] == "*")
						return true;
					return $action == $item['p_action'];
				}
			}
			return false;
		};
		$deny_list = array_filter($list, function($item){
			return $item['p_deny'];
		});

		$isDeny = count($deny_list) > 0
			? count(array_filter($deny_list, $f)) > 0
			: 0;

		if($isDeny){
			return false;
		}

		$allow_list = array_filter($list, function($item){
			return $item['p_allow'];
		});
		$isAllow = count($allow_list) > 0
			? count(array_filter($allow_list, $f)) > 0
			: 0;
		if($isAllow){
			return true;
		}

		return false;
	}
	protected function getMyPermission($uid){
		$roleQuery = $this->db->createSqlBuilder('role_user')
			->where(array(
				'user_id'=>$uid
			))
			->fields('role_id')
			->query();

		if(!$roleQuery)
			return array();
		$roleList = array_map(function($item){
			return $item['role_id'];
		}, $roleQuery);

		$where1 = array(
			'p_type' => 1,
			'p_target_id' => $uid,
		);

		if($roleList) {
			$where2 = array(
				'p_type' => 2,
				'p_target_id' => Dbase::InOperate($roleList),
			);

			$where = array(
				$where1,
				'_op_' => 2,
				$where2,
			);
			$result = $this->db->createSqlBuilder('permission')
				->where($where)->query();
		}else{
			$result = $this->db->createSqlBuilder('permission')
				->where($where1)->query();
		}
		return $result;
	}
}