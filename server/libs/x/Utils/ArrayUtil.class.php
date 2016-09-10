<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 23:58
 */

namespace x\Utils;


class ArrayUtil {

	//region 静态方法
	public static function create(array $arr = null) {
		if ($arr) {
			return new xArray($arr);
		}
		return new xArray(arrar());
	}

	/**
	 * 递归合并数组
	 * 如果传入的参数都不是数组，则返回一个由这些值组成的简单数组（数字索引）
	 * 如果没有传值返回false
	 * 如果传入的值有部分不是数组，会自动添加到最后（会忽略：null）
	 * @return false|bool|mixed
	 */
	public static function merge() {
		$argc_tmp = func_num_args();
		if ($argc_tmp < 1) {
			return false;
		}
		$argv_tmp = func_get_args();
		$argv = array_filter($argv_tmp, function ($item) {
			return is_array($item);
		});
		$argc = count($argv);
		if ($argc == 0) {
			return $argv_tmp;
		}
		if ($argc != $argc_tmp) {
			$no_array = array_filter($argv_tmp, function ($item) {
				return !is_array($item) && $item!=null;
			});
			array_push($argv, $no_array);
		}
		$last = array_pop($argv);
		while (count($argv) > 0) {
			$then = array_pop($argv);
			foreach ($last as $key => $value) {
				if (!array_key_exists($key, $then)) {
					$then[$key] = $value;
					continue;
				}
				if (is_array($value)) {
					$then[$key] = self::merge($then[$key], $value);
				} else {
					$then[$key] = $value;
				}
			}
			$last = $then;
		}
		return $last;
	}

	/**
	 * 根据条件筛选然后返回第一个项目
	 * @param array $arr
	 * @param       $handler
	 * @return mixed
	 */
	public static function first(array $arr, $handler) {
		$tmp = array_filter($arr, $handler);
		$tmp = array_reverse($tmp);
		return array_pop($tmp);
	}

	/**
	 * 根据条件筛选然后返回最后一个项目
	 * @param array $arr
	 * @param       $handler
	 * @return mixed
	 */
	public static function last(array $arr, $handler) {
		$tmp = array_filter($arr, $handler);
		return array_pop($tmp);
	}
	//endregion
}