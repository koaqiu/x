<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/6
 * Time: 9:55
 */

namespace x\Utils;

/**
 * 字符串扩展方法
 * @package x\Utils
 */
class xString {

	/**
	 * 把JSON字符串解码
	 * @param string $str
	 * @return bool|mixed
	 */
	public static function FromJson($str) {
		if (!$str || !is_string($str) || strlen($str) < 1) {
			return false;
		}
		return json_decode($str);
	}

	/**
	 * 移除字符串头部的指定的字符（默认是空白字符“ ”）
	 * @param  string $str
	 * @param string  $toRemove
	 * @return mixed
	 */
	public static function LeftTrim($str, $toRemove = " ") {
		if (!$str) {
			return $str;
		}
		return preg_replace("/^(" . $toRemove . ")+/S", "", $str);
	}

	/**
	 * 移除字符串尾部的指定的字符（默认是空白字符“ ”）
	 * @param string $str
	 * @param string $toRemove
	 * @return mixed
	 */
	public static function RightTrim($str, $toRemove = " ") {
		if (!$str) {
			return $str;
		}
		return preg_replace("/(" . $toRemove . ")+$/S", "", $str);
	}

	/**
	 * 移除字符串头尾的指定的字符（默认是空白字符“ ”）
	 * @param string $str
	 * @param string $toRemove
	 * @return mixed
	 */
	public static function Trim($str, $toRemove = " ") {
		if (!$str) {
			return $str;
		}
		return self::LeftTrim(self::RightTrim($str, $toRemove), $toRemove);
	}

	/**
	 * 分隔字符串
	 * @param string $str       要分隔的字符串
	 * @param string $delimiter 分隔字符
	 * @param null   $limit
	 * @return array
	 */
	public static function Split($str, $delimiter, $limit = null) {
		return $limit ? explode($delimiter, $str, $limit)
			: explode($delimiter, $str);
	}
}