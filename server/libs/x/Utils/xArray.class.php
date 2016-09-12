<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/10
 * Time: 10:30
 */

namespace x\Utils;

/**
 * 封装一些数组操作
 * Class xArray
 * @package x\Utils
 */
class xArray {
	private $array_data;
	/**
	 * 数组长度
	 * @var int
	 */
	private $length = 0;

	function __construct(array $array) {
		$this->array_data = $array;
	}

	//region 魔法方法
	function __get($name) {
		switch ($name) {
			case 'length':
				$this->length = count($this->array_data);
				return $this->length;
		}
		return false;
	}

	function __set($name, $value) {
		switch ($name) {
			case 'length':
				throw new \ErrorException("$name 是只读的");
				break;
		}
		return $this;
	}

	//endregion

	/**
	 * 遍历数组执行 $handler，会修改原数组！
	 * @param $handler ($item){return $item};
	 * @return $this
	 */
	public function map($handler) {
		$this->array_data = array_map($handler, $this->array_data);
		return $this;
	}

	/**
	 * 遍历数组执行 $handler，不会对原数组做任何事情
	 * @param           $handler  ($item){return $item};
	 * @param xArray    $outArray 如果指定则返回处理过的新数组
	 * @return $this
	 */
	public function each($handler, &$outArray = null) {
		switch (func_num_args()) {
			case 1:
				array_map($handler, $this->array_data);
				break;
			default:
				$tmp = array_map($handler, $this->array_data);
				$outArray = new xArray($tmp);
		}

		return $this;
	}

	public function keyExists($key) {
		return array_key_exists($key, $this->array_data);
	}

	/**
	 * 返回数组中第一个记录
	 * @param null $handler 如果有指定则根据这个条件查找数组
	 * @return null|mixed
	 */
	public function first($handler = null) {
		if (count($this->array_data) < 1) {
			return null;
		}
		if ($handler) {
			$newArray = array_filter($this->array_data, $handler);
		} else {
			$newArray = array_slice($this->array_data, 0, 1);
		}
		return array_pop($newArray);
	}

	/**
	 * 返回数组中最后一个记录
	 * @param null $handler 如果有指定则根据这个条件查找数组
	 * @return bool|mixed
	 */
	public function last($handler = null) {
		if (count($this->array_data) < 1) {
			return null;
		}
		if ($handler) {
			$newArray = array_filter($this->array_data, $handler);
		} else {
			$newArray = array_slice($this->array_data, count($this->array_data) - 1, 1);
		}
		return array_pop($newArray);
	}

	/**
	 * 根据handler查找数组
	 * @param $handler ($item){return bool}
	 * @return xArray   返回一个新的数组
	 */
	public function find($handler) {
		return new xArray(array_filter($this->array_data, $handler));
	}

	public function get($key) {
		return $this->array_data[$key];
	}

	public function add($key, $value) {
		$this->array_data[$key] = $value;
		return $this;
	}

	public function push() {
		$argc = func_num_args();
		if ($argc > 0) {
			for ($i = 0; $i < $argc; $i++)
				$this->array_data[] = func_get_arg($i);
		}
		return $this;
	}

	public function pop() {
		return array_pop($this->array_data);
	}

	public function toArray() {
		return $this->array_data;
	}

	public function toList() {
		return array_values($this->array_data);
	}
}