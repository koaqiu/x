<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/30
 * Time: 11:05
 */

namespace x\Db\Utils;


class Table {
	private $_tableName;
	private $_alias;

	function __construct($tableName, $alias = "") {
		$this->_tableName = $tableName;
		$this->_alias = $alias;
	}

	public function getName(){
		return $this->_tableName;
	}
	public function getAlias(){
		return $this->_alias;
	}
}