<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/30
 * Time: 14:10
 */

namespace x\Db\Utils;


class JoinTable extends Table {
	private $_leftTableField;
	private $_rightTableField;

	function __construct($tableName, $alias, $leftTableField, $rightTableField) {
		parent::__construct($tableName, $alias);
		$this->_leftTableField = $leftTableField;
		$this->_rightTableField = self::checkField($alias, $rightTableField);
	}

	public function getLeftTableField(){
		return $this->_leftTableField;
	}
	public function getRightTableField(){
		return $this->_rightTableField;
	}

	/**
	 * 检查是否合法
	 * @param string $tableName 表名（或别名）
	 * @param string $field
	 * @return string
	 * @throws \ErrorException
	 */
	public static function checkField($tableName, $field){
		if(strpos($field, ".") === false){
			return "$tableName.$field";
		}
		$arr = explode(".", $field);
		if(strcasecmp($arr[0], $tableName) == 0)
			return $field;
		throw new \ErrorException("校验失败：$tableName, $field");
	}
}