<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/10/15
 * Time: 15:03
 */

namespace x\Utils;

/**
 * 解析 Uri
 * @package x\Utils
 */
class Uri {

	private $_url;
	private $_scheme;
	private $_host;
	private $_port;
	private $_user;
	private $_pass;
	private $_path;
	private $_file;
	private $_query;
	private $_fragment;


	/**
	 * Uri constructor.
	 * @param string $url
	 * @throws \ErrorException
	 */
	function __construct($url) {
		$arr = parse_url($url);
		if (empty($arr)) {
			throw new \ErrorException('非法Uri');
		}
		$arr = new Data($arr);
		$this->_url = $url;
		$this->_scheme = strtolower($arr->get('scheme'));
		$this->_host = strtolower($arr->get('host'));
		$this->_port = $arr->get('port');
		if ($this->_port < 1) {
			if (isset(UriBuilder::$DEFAULT_PORT[$this->_scheme])) {
				$this->_port = UriBuilder::$DEFAULT_PORT[$this->_scheme];
			} else {
				$this->_port = null;
			}
		}
		$this->_user = $arr->get('user');
		$this->_pass = $arr->get('pass');
		$this->_path = $arr->get('path');
		$this->_query = $arr->get('query');
		$this->_fragment = $arr->get('fragment');

		$paths = explode('/', $this->_path);
		if(!empty($paths)) {
			$file = array_pop($paths);
			if(!empty($file)) {
				$chars = str_split($file);
				if (strcmp(array_pop($chars), '/') != 0) {
					$this->_file = $file;
				}
			}
		}
	}

	//region Query
	/**
	 * query - 在问号 ? 之后
	 * @return string
	 */
	public function getQueryString() {
		return $this->_query;
	}

	public function getQuery() {
		parse_str($this->_query, $result);
		return $result;
	}

	/**
	 * 指定的参数是否存在
	 * @param $key
	 * @return bool
	 */
	public function existsQuery($key) {
		parse_str($this->_query, $result);
		return array_key_exists($key, $result);
	}
	//endregion

	/**
	 * fragment - 在散列符号 # 之后
	 * @return string
	 */
	public function getFragmentString() {
		return $this->_fragment;
	}

	public function getFragment() {
		parse_str($this->_fragment, $result);
		return $result;
	}

	//region 返回基本属性
	/**
	 * 返回协议
	 * @return string
	 */
	public function getScheme() {
		return $this->_scheme;
	}
	public function getHost() {
		return $this->_host;
	}
	public function getPath() {
		return $this->_path;
	}
	public function getFile() {
		return $this->_file;
	}

	public function getUser() {
		return $this->_user;
	}

	public function getPass() {
		return $this->_pass;
	}

	/**
	 * 端口号
	 * @return number
	 */
	public function getPort() {
		return $this->_port;
	}

	//endregion

	public function getBuilder(){
		$builder = new UriBuilder();
		$builder->setScheme($this->_scheme)
			->setUser($this->_user)
			->setPass($this->_pass)
			->setHost($this->_host)
			->setPort($this->_port)
			->setPath($this->_path)
			->setQueryString($this->_query)
			->setFragmentString($this->_fragment);
		return $builder;
	}
	function __toString() {
		return $this->getBuilder()->build();
	}
}