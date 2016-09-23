<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/23
 * Time: 11:26
 */

namespace x\Core\Secure;


interface IPermission {

	/**
	 * 检查当前用户的权限，有权限返回true，否则返回false
	 * @param $module
	 * @param $controller
	 * @param $action
	 * @return boolean
	 */
	function check($module, $controller, $action);
}