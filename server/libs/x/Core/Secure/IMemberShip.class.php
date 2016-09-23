<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/23
 * Time: 15:11
 */

namespace x\Core\Secure;


interface IMemberShip {
	/**
	 * 获取当前用户ID
	 * @return int
	 */
	function getUserId();
	/**
	 * 获取当前用户名
	 * @return string
	 */
	function getUserName();

	/**
	 * 退出系统的时候调用，用来清理用户信息
	 * @return void
	 */
	function logout();
	/**
	 * 设置用户信息，一般是用户成功登录以后一次性写入相关信息
	 * @param array $user
	 * @return void
	 */
	function setUser(array $user);
}