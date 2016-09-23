<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/23
 * Time: 14:37
 */

namespace x\Core;
use x\Core\Secure\IMemberShip;
use x\Core\Secure\MemberShip;

/**
 * Class Factory
 * @package x\Core
 */
class Factory {
	private static $_facotries = array();

	/**
	 * 查找并创建工厂（单例）
	 * @param string     $className
	 * @param array|null $check_implements
	 * @return null
	 */
	public static function getFacotry($className, array $check_implements = null) {
		if(empty($className))
			return null;
		if (array_key_exists($className, self::$_facotries)) {
			return self::$_facotries[$className];
		}
		$found = \CLoader::findFile(\CLoader::getClassName($className));
		if ($found && count($check_implements) > 0) {
			$implements = class_implements($className);
			if (array_intersect($implements, $check_implements)) {
				// do not things
			}
			return null;
		}
		$factory =  new $className;
		self::$_facotries[$className] = $factory;
		return $factory;
	}

	/**
	 * @param $config
	 * @return IMemberShip
	 */
	public static function getMemberShip($config){
		$handler = $config['memberShip'];
		$factory = Factory::getFacotry($handler, array('x\Core\Secure\IMemberShip'));
		if($factory)
			return $factory;
		else
			return new MemberShip();
	}
}