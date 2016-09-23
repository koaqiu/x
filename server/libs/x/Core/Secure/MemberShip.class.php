<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/23
 * Time: 15:21
 */

namespace x\Core\Secure;


class MemberShip implements IMemberShip {

	/**
	 * 获取当前用户ID
	 * @return int
	 */
	function getUserId() {
		return $this->getIntSession('user_id');
	}

	/**
	 * 获取当前用户名
	 * @return string
	 */
	function getUserName(){
		return $this->getSession("user_name", "");
	}
	/**
	 * 退出系统的时候调用，用来清理用户信息
	 * @return void
	 */
	function logout(){
		$this->removeSession('user_id');
		$this->removeSession('user_name');
	}
	/**
	 * 设置用户信息，一般是用户成功登录以后一次性写入相关信息
	 * @param array $user
	 * @return void
	 */
	function setUser(array $user){
		$_SESSION['user_id'] = $user['user_id'];
		$_SESSION['user_name'] = $user['user_name'];
	}

	protected function removeSession($key){
		$_SESSION[$key] = null;
	}
	protected function getSession($key, $dv = null){
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $dv;
	}
	protected function getIntSession($key, $dv = 0){
		$result  = $this->getSession($key, $dv);
		return is_numeric($result) ? intval($result) : $dv;
	}
}