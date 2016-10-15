<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/10/15
 * Time: 16:50
 */

namespace x\Utils;


/**
 * 构建 Uri
 * @package x\Utils
 */
class UriBuilder {

	/**
	 * 默认端口号
	 * @var array
	 */
	static $DEFAULT_PORT = array(
		'http' => 80,
		'https' => 443,
		'ftp' => 21,
		'ssh' => 22,
	);

	private $_scheme = 'http';
	private $_host;
	private $_port = 80;
	private $_user;
	private $_pass;
	private $_path;
	private $_query;
	private $_fragment;

	public function setScheme($value) {
		if(array_key_exists($value, self::$DEFAULT_PORT)) {
			$this->_scheme = $value;
		}else{
			throw new \ErrorException('非法的协议');
		}
		return $this;
	}
	public function setHost($value) {
		$this->_host = urlencode(strtolower($value));
		return $this;
	}
	public function setPath($value) {
		if(empty($value)) return $this;
		$chars = str_split($value);
		if (strcmp($chars[0], '/') != 0) {
			$value = "/".$value;
		}
		$this->_path = $value;
		return $this;
	}

	public function setUser($value) {
		$this->_user = urlencode($value);
		return $this;
	}

	public function setPass($value) {
		$this->_pass = urlencode($value);
		return $this;
	}

	/**
	 * 端口号
	 * @param number $value
	 * @return UriBuilder
	 * @throws \ErrorException
	 */
	public function setPort($value) {
		if($value < 0 || $value > 65535){
			throw new \ErrorException('端口号非法（1-65535）');
		}
		$this->_port = $value;
		return $this;
	}

	//region Query
	/**
	 * 删除参数
	 * @param $key
	 * @return UriBuilder
	 */
	public function removeQuery($key) {
		parse_str($this->_query, $result);
		$filtered = array_filter(
			$result,
			function ($ikey) use ($key) {
				return strcmp($ikey, $key) != 0;
			},
			ARRAY_FILTER_USE_KEY
		);
		$this->_query = http_build_query($filtered);
		return $this;
	}

	/**
	 * 添加参数到Query，如果$key已经存在则会升级成数组
	 * @param string $key
	 * @param string $value
	 * @return UriBuilder
	 */
	public function addQuery($key, $value) {
		parse_str($this->_query, $result);
		if (array_key_exists($key, $result)) {
			$arr[] = $result[$key];
			$arr[] = $value;
			$result[$key] = $arr;
		} else {
			$result[$key] = $value;
		}
		$this->_query = http_build_query($result);
		return $this;
	}

	/**
	 * 设置参数，如果$key已经存在则会覆盖
	 * 如果只传递一个array参数，则把这个数组里面的值都添加到Query中
	 * @param string|array $key
	 * @param string|null $value
	 * @return UriBuilder
	 */
	public function setQuery($key, $value = null) {
		if(is_array($key)){
			foreach ($key as $kk => $vv){
				$this->setQuery($kk, $vv);
			}
		}else {
			parse_str($this->_query, $result);
			$result[$key] = $value;
			$this->_query = http_build_query($result);
		}
		return $this;
	}
	public function setQueryString($value){
		$this->_query = $value;
		return $this;
	}

	//endregion

	public function setFragmentString($value){
		$this->_fragment = $value;
		return $this;
	}
	/**
	 * 构建 Uri
	 * @return string
	 * @throws \ErrorException
	 */
	public function build(){
		if(empty($this->_host)){
			throw new \ErrorException('必须指定主机（Host）');
		}
		$url = $this->_scheme;
		$url .= "://";
		if (!empty($this->_user)) {
			$url .= $this->_user;
			if (!empty($this->_pass)) {
				$url .= ":" . $this->_pass;
			}
			$url .= "@";
		}
		$url .= $this->_host;
		if ($this->_port > 0) {
			if (isset(self::$DEFAULT_PORT[$this->_scheme]) && self::$DEFAULT_PORT[$this->_scheme] != $this->_port) {
				$url .= ":$this->_port";
			}
		}
		$url .= $this->_path;
		if (!empty($this->_query)) {
			$url .= "?" . $this->_query;
		}
		if (!empty($this->_fragment)) {
			$url .= "#" . $this->_fragment;
		}
		return $url;
	}
	public function getUri(){
		return new Uri($this->build());
	}
}