<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 10:25
 */

namespace x\Db;
use x\Db\Operates\BetweenOperate;
use x\Db\Operates\EgtOperate;
use x\Db\Operates\EltOperate;
use x\Db\Operates\EqOperate;
use x\Db\Operates\GtOperate;
use x\Db\Operates\InOperate;
use x\Db\Operates\IsNotNullOperate;
use x\Db\Operates\IsNullOperate;
use x\Db\Operates\LikeOperate;
use x\Db\Operates\LtOperate;
use x\Db\Operates\NeqOperate;
use x\Db\Operates\NotBetweenOperate;
use x\Db\Operates\NotInOperate;
use x\Db\Operates\NotLikeOperate;
use x\Db\Operates\WhereItem;
use x\Db\Operates\WhereOperate;
use x\Db\Utils\JoinTable;
use x\Db\Utils\Table;
use x\Utils\DateTime;


/**
 * Class SqlBuilder
 * Sql语句生成器
 * @package x\Db
 */
class SqlBuilder
{
    const ACTION_SELECT = 1;
    const ACTION_INSERT = 2;
    const ACTION_UPDATE = 3;
    const ACTION_DELETE = 4;

    const OPERATOR_AND = 1;
    const OPERATOR_OR = 2;

    static $WHITE_FUNCTION_LIST = array(
        "DATE_FORMAT","SUM","MIN","COUNT","MAX"
    );

    private $_alias;
    private $_fields;
    private $_orderBy;
    private $_groupBy;
    private $_tableName;
    private $_action;
    private $_where;
    private $_values;
    private $_limit = 1000;
    private $_skip = 0;
    private $_isLockField = false;
	private $_joins;

    private $_db;
    private function __construct(Dbase $db, $action = self::ACTION_SELECT)
    {
        $this->_db = $db;
        $this->_fields = array();
        $this->_orderBy = array();
        $this->_groupBy = array();
	    $this->_joins = array();
        $this->_action = $action;
    }

    public function isSelect(){
        return $this->_action == self::ACTION_SELECT;
    }

	/**
	 * 是否是子查询，条件：
	 * 1 是 SELECT 查询语句
	 * 2 只有一个字段且非“*”
	 * @return bool
	 */
    public function isSubSelect(){
        return $this->_action == self::ACTION_SELECT
            && count($this->_fields) && $this->_fields[0] != "*";
    }

    //region 创建Builder的快捷方法
    public static function Select(Dbase $db, $tableName){
        $builder = new SqlBuilder($db, self::ACTION_SELECT);
        $builder->_tableName = $builder->fixField($tableName);
        return $builder;
    }

//    /**
//     * SELECT COUNT(*) 的快捷操作
//     * @param Dbase $db
//     * @param $tableName
//     * @return SqlBuilder
//     */
//    public static function Count(Dbase $db, $tableName){
//        $builder = new SqlBuilder($db, self::ACTION_SELECT);
//        $builder->_tableName = $builder->fixField($tableName);
//        $builder->fields("COUNT(*)");
//        $builder->lockField();
//        return $builder;
//    }
    public static function Insert(Dbase $db, $tableName){
        $builder = new SqlBuilder($db, self::ACTION_INSERT);
        $builder->_tableName = $tableName;
        return $builder;
    }
    public static function Update(Dbase $db, $tableName){
        $builder = new SqlBuilder($db, self::ACTION_UPDATE);
        $builder->_tableName = $tableName;
        return $builder;
    }
    public static function Delete(Dbase $db, $tableName){
        $builder = new SqlBuilder($db, self::ACTION_DELETE);
        $builder->_tableName = $tableName;
        return $builder;
    }
    //endregion

	/**
	 * 限制输出的记录数，一般用来分页
	 * @param int $limit
	 * @param int $skip
	 * @return SqlBuilder
	 */
    public function limit($limit = 10, $skip = 0){
        $this->_limit = is_numeric($limit) ?intval($limit) : 10;
        $this->_skip = is_numeric($skip) ?intval($skip) : 0;
        return $this;
    }

	/**
	 * 分页，设置页码
	 * @param int $page
	 * @param int $limit
	 * @return SqlBuilder
	 */
    public function setPage($page = 1, $limit = 10){
	    $page = is_numeric($page) ? max(intval($page), 1) : 1;
	    $limit = is_numeric($limit) ? max(intval($limit), 10) : 10;
	    $skip = ($page - 1) * $limit;
	    return $this->limit($limit, $skip);
    }
    public function lockField(){
        $this->_isLockField = true;
    }

    /**
     * 设置或者获取别名
     * @param string|bool $alias
     * @return SqlBuilder|string
     */
    public function alias($alias = false){
        if($alias) {
            $this->_alias = $alias;
            return $this;
        }
        return $this->_alias;
    }
    /**
     * 指定字段，执行该方法会把传入的字段添加到列表中。多次执行会多次添加
     * @param $fields
     * @return SqlBuilder
     */
    public function fields($fields){
        if($this->_isLockField)
            return $this;
        if(is_array($fields)){
            array_map(function($field){
                $this->fields($field);
            }, $fields);
        }elseif (is_string($fields)) {
            if (trim($fields) == "*") {
                $this->_fields = array("*");
            } else {
                array_push($this->_fields, trim($fields));
            }
        }elseif($fields instanceof SqlBuilder){
            array_push($this->_fields, $fields);
        }else{
            $this->fields((string)$fields);
        }
        return $this;
    }

    /**
     * 设置查询条件
     * @param array $where
     * @return SqlBuilder
     */
    public function where(array $where){
        $this->_where = $where;
        return $this;
    }

	/**
	 * 设置值（插入（INSERT INTO）或更新（UPDATE）语句有效）
	 * @param array $values
	 * @return SqlBuilder
	 */
    public function values(array $values){
        $this->_values = $values;
        return $this;
    }

	/**
	 * 连接数据表，当前表必须指定别名
	 * @param string $table           要连接的表
	 * @param string $alias           别名
	 * @param string $leftTableField  左表（主表）字段名 最好使用“xx.xx”形式
	 * @param string $rightTableField 右表（链接的表之前的$table指定的）字段名 最好使用“xx.xx”形式
	 * @return SqlBuilder
	 * @throws \ErrorException  主表没有指定别名会报错，非 SELECT 语句会报错
	 */
	public function joinTable($table, $alias, $leftTableField, $rightTableField) {
		if (empty($this->_alias)) {
			throw new \ErrorException("请为主表指定别名“alias”");
		}
		if ($this->_action != self::ACTION_SELECT) {
			throw new \ErrorException("必须是 SELECT 命令");
		}
		array_push($this->_joins, new JoinTable($table, $alias, $leftTableField, $rightTableField));
		return $this;
	}

    /**
     * 设置排序，支持多参数
     * @param array|string $item 可以是一个字符串或者一个数组或者多个字符串参数，其他情况直接忽略
     * @return SqlBuilder
     */
    public function orderBy($item){
        if(func_num_args() > 1){
            return $this->orderBy(func_get_args());
        }
        if(is_string($item)){
            $item = trim($item);
	        if(strpos($item, ",")){
		        $arr = explode(",", $item);
		        array_map(function($i){
			        $this->orderBy($i);
		        }, $arr);
	        }else if(strpos($item, ' ')) {
		        $arr = explode(' ', $item);
		        $dir = array_pop($arr);
		        //DESC,ASC
		        if (strcasecmp($dir, 'DESC') !== false) {
			        $this->_orderBy[] = "`" . $this->fixField(rtrim($item, $dir)) . "` DESC";
		        } elseif (strcasecmp($dir, 'ASC') !== false) {
			        $this->_orderBy[] = "`" . $this->fixField(rtrim($item, $dir)) . "` ASC";
		        }
	        }else if(empty($item)){
	        	//空的 不做任何事情
            }else{
                $this->_orderBy[] = "`".$this->fixField($item). "` ASC";
            }
        }else if(is_array($item)){
            array_map(function($i){
                $this->orderBy($i);
            }, $item);
        }
        return $this;
    }

	/**
	 * 清除排序
	 * @return SqlBuilder
	 */
	public function clearOrderBy(){
		$this->_orderBy = array();
		return $this;
	}
	/**
	 * 分组
	 * @param $field
	 * @return SqlBuilder
	 */
    public function groupBy($field){
        if(func_num_args() > 1){
            return $this->groupBy(func_get_args());
        }
        array_push($this->_groupBy, $this->_db->EscapeString($field));
        return $this;
    }

    protected function buildWhere(array $where = null, $isTop = true, $level = 1){
        if($where == null){
            $where = $this->_where;
        }
        if($where && is_array($where)){
            if(array_key_exists('_op_', $where) && count($where) == 1){
                return "";
            }
            $str = "";
	        $tab = "\n".str_repeat("\t", $level);
            $operator = $tab."AND";
            foreach ($where as $key=>$item) {
                if(strcasecmp($key, '_op_') == 0){
                    switch ($item){
                        case self::OPERATOR_OR:$operator = $tab."OR";break;
                        default:$operator = $tab."AND";break;
                    }
                    continue;
                }
                if(is_array($item)){
                    $result = $this->buildWhere($item, false, $level + 1);
                    if($result)
                        $str .= " $operator ".$result."";
                    continue;
                }
                if(is_object($item)){
                    if(is_subclass_of($item,"x\\Db\\Operates\\WhereOperate")) {
                        $field = $this->fixField($key);
                        $str .= " $operator `$field` " . $item->build();
                    }elseif ($item instanceof WhereItem){
                        $str .= " $operator ". $item->build();
                    }
                }else{
                    $field = $this->fixField($key);
                    $str .= " $operator `$field` = ". WhereOperate::fixValue($item);
                }
            }
            $str = preg_replace("/^\\s+(or|and)\\s+/i","", $str);
            return  $isTop
                        ? "\nWHERE $tab".$str
                        : "(".$tab.$str.$tab.")";
        }
        return "";
    }

    protected function buildSelect($isSub = false){
        $sql = "SELECT \n\t";
        if(count($this->_fields) == 0){
            $sql .= " * ";
        }else {
            $sql .= join(", ", array_map(function($item){
                return $this->fixField($item);
            }, $this->_fields));
        }
        if(!empty($this->_tableName)) {
        	$tableName = $this->fixField($this->_tableName, true);
        	if(empty($this->_alias)) {
		        $sql .= " \nFROM $tableName ";
	        }else{
	        	$alias = $this->fixField($this->_alias, true);
		        $sql .= " \nFROM $tableName AS $alias";
	        }
        }
        // 链表 JOIN
	    $sql .= $this->buildJoin();
        $sql .= $this->buildWhere();
        if(count($this->_groupBy) > 0){
            $sql .= "\nGROUP BY ".join(', ', $this->_groupBy);
        }

        if(count($this->_orderBy) > 0) {
            $sql .= "\nORDER BY \n\t".join(",\n\t", $this->_orderBy);
        }

        if($isSub){
            $sql .= "\nLIMIT 0,1";
        }else {
            $sql .= "\nLIMIT $this->_skip, $this->_limit";
        }
        //var_dump($sql);die();
        return $sql;
    }
    protected function buildDelete(){
        $sql = "DELETE FROM \n";
        $sql .= "\t ".$this->fixField($this->_tableName)." ";
        $sql .= $this->buildWhere();
        if(stripos ($sql, "WHERE") === false){
            throw new \ErrorException("没有条件的删除（DELETE）语句非常危险，如果确定要使用请设置“1=1”为条件！");
        }
        return $sql;
    }
    protected function buildUpdate(){
        $this->checkFieldAndValueForUpdate();
        $sql = "UPDATE \n";
        $sql .= "\t".$this->fixField($this->_tableName);
	    $sql .= "\nSET ";

        $fields = array_values($this->_fields);
        $values = array_values($this->_values);

        $length = count($fields);
        $data = array();
        for ($i = 0; $i < $length; $i++){
            $field = $this->fixField($fields[$i]);
            $item = $values[$i];

            if(is_numeric($item)){
                array_push($data, "`".$field."` = ".$item);
            }else if(is_string($item)){
                array_push($data, "`".$field."` = '".$item."'");
            }else if(is_bool($item)){
                if($item === true) {
                    array_push($data, "`".$field."` = TRUE");
                }else if($item === false) {
                    array_push($data, "`".$field."` = FALSE");
                }
            }
        }

        $sql .= "\n\t".join(",\n\t", $data);
        $sql .= " ".$this->buildWhere();
        if(stripos ($sql, "WHERE") === false){
            throw new \ErrorException("没有条件的更新（UPDATE）语句非常危险，如果确定要使用请设置“1=1”为条件！");
        }
        return $sql;
    }
    protected function buildInsert(){
        $this->checkFieldAndValueForInsert();

        $sql = "INSERT INTO ";
        $sql .= $this->fixField($this->_tableName);
        $sql .= "\n\t(".join(", ", array_map(function($item){
            return $this->fixField($item);
        }, $this->_fields));
        $sql .= " ) \nVALUES\n\t( ";
        $sql .= join(", ", $this->fixValues($this->_values));
        $sql .= " )";

        return $sql;
    }

	/**
	 * 处理链表
	 * @return string
	 */
    protected function buildJoin(){
	    if(count($this->_joins) > 0){
	    	return "\n".join("\n",
				array_map(function(JoinTable $item){
					$tableName = $this->fixField($item->getName());
					$alias = $this->fixField($item->getAlias());
					$field1 = $this->fixField($item->getLeftTableField(), true);
					$field2 = $this->fixField($item->getRightTableField(), true);
					return "\tJOIN `$tableName` AS `$alias` ON $field1 = $field2";
				}, $this->_joins));
	    }
	    return '';
    }

    protected function checkFieldAndValueForUpdate(){
        if(!is_array($this->_values) || count($this->_values) < 1){
            throw new \ErrorException("没有指定值（Value）！");
        }
        $count_fields = count($this->_fields);
        $count_values = count($this->_values);
        if ($count_fields != $count_values){
            throw new \ErrorException("字段和值的数量不匹配！");
        }
    }
    protected function checkFieldAndValueForInsert(){
        if(!is_array($this->_values) || count($this->_values) < 1){
            throw new \ErrorException("没有指定值（Value）！");
        }
        $count_fields = count($this->_fields);
        $count_values = count($this->_values);
        if ($count_fields == 1 && $this->_fields[0] != "*" && $count_fields != $count_values){
            throw new \ErrorException("字段和值的数量不匹配！");
        }
    }

    protected function fixValues(array $values){
        $data = array();
        foreach ($values as $key=>$item) {
            if(is_numeric($item)){
                array_push($data, $item);
            }else if(is_string($item)){
                array_push($data, "'".$this->fixValue($item)."'");
            }else if(is_bool($item)){
                array_push($data, $item);
            }else if($item instanceof \DateTime){
                //date('Y-m-d H:i:s',time())
                array_push($data, "'".$item->format('Y-m-d H:i:s')."'");
            }else if($item instanceof DateTime){
	            array_push($data, "'".$item->format('Y-m-d H:i:s')."'");
            }
        }
        return $data;
    }
    protected function fixValue($value){
        if(!is_string($value)){
            throw new \ErrorException("非法字段：".$value);
        }
        return $this->_db->EscapeString($value);
    }
    protected function fixField($field, $addQuotation = false){
        if($field instanceof SqlBuilder){
            return "(".$field->buildSelect(true).")".
            (is_string($field->_alias) ? (" AS ".$field->alias()):"");
        }
        if(!is_string($field)){
            throw new \ErrorException("非法字段：");
        }
        $field = trim($field);
        if(preg_match('/([a-z_]{1,}) {0,}\(( {0,}.{1,},{0,1}){1,}\)/iS', $field, $m)){
            if(in_array($m[1], self::$WHITE_FUNCTION_LIST)){
                $params = explode(",", $m[2]);
                $params = array_map(function($item) use($addQuotation){
                    $item = trim($item);
                    if(preg_match("/^'(.+)'$/", $item, $item_m)){
                        return "'".$this->_db->EscapeString($item_m[1])."'";
                    }else{
                        return $this->_db->EscapeString($item, $addQuotation);
                    }
                }, $params);
                if(strlen($m[0]) == strlen($field))
                    return $m[1]."(".join(',',$params).")";
                else{
                    $str = trim(substr($field, strlen($m[0])));
                    if(stripos($str, "AS") === false){
                        return $m[1]."(".join(',',$params).") AS ".$this->_db->EscapeString($str, $addQuotation);
                    }else{
                        return $m[1]."(".join(',',$params).") ".$this->_db->EscapeString($str, $addQuotation);
                    }
                }
            }
        };

        $arr = null;
	    if(strpos($field," AS ") > 0){
	    	$arr = explode(" AS ", $field);
	    }elseif(strpos($field," as ") > 0){
		    $arr = explode(" as ", $field);
	    }elseif(strpos($field," ") > 0){
		    $arr = explode(" ", $field);
	    }else{
	    	$arr[] = $field;
	    }
	    if(strpos($arr[0], ".") > 0){
	    	$a1 = explode(".", $arr[0]);
		    $arr[0] = $this->_db->EscapeString($a1[0], $addQuotation).".".$this->_db->EscapeString($a1[1], $addQuotation);
	    }
        if(count($arr) > 1){
        	return $this->_db->EscapeString($arr[0], $addQuotation)
		        ." AS ".
		        $this->_db->EscapeString($arr[1], $addQuotation);
        }
        return $this->_db->EscapeString($arr[0], $addQuotation);
    }

	/**
	 * 生成Sql语句
	 * @return string
	 */
    public function build(){
        switch ($this->_action){
            case self::ACTION_SELECT:
                return $this->buildSelect();
                break;
            case self::ACTION_INSERT:
                return $this->buildInsert();
                break;
            case self::ACTION_UPDATE:
                return $this->buildUpdate();
                break;
            case self::ACTION_DELETE:
                return $this->buildDelete();
                break;
        }
        return "";
    }

    public function execute(){
        $sql = $this->build();
        return $this->_db->execute($sql);
    }
    public function query(){
        $sql = $this->build();
        return $this->_db->query($sql);
    }
    public function firstLine(){
        $sql = $this->build();
        return $this->_db->firstLine($sql);
    }

    public function count(){
    	$old = $this->_fields;
	    $this->_fields = array();
        $this->fields("COUNT(*)");
        $result = $this->first();
	    $this->_fields = $old;
	    return $result;
    }
	public function max($field){
		$old = $this->_fields;
		$this->_fields = array();
		$this->fields("MAX($field)");
		$result = $this->first();
		$this->_fields = $old;
		return $result;
	}
	public function min($field){
		$old = $this->_fields;
		$this->_fields = array();
		$this->fields("MIN($field)");
		$result = $this->first();
		$this->_fields = $old;
		return $result;
	}
	public function sum($field){
		$old = $this->_fields;
		$this->_fields = array();
		$this->fields("SUM($field)");
		$result = $this->first();
		$this->_fields = $old;
		return $result;
	}
    public function first(){
        $sql = $this->build();
        $result = $this->_db->firstLine($sql);
        if($result){
            return array_pop($result);
        }
        return false;
    }

	/**
	 * 复制
	 * @return SqlBuilder
	 */
    public function copy(){
        $builder = new SqlBuilder($this->_db,$this->_action);
        $builder->_alias = $this->_alias;
        $builder->_fields = $this->_fields;
        $builder->_orderBy = $this->_orderBy;
        $builder->_groupBy = $this->_groupBy;
        $builder->_tableName = $this->_tableName;
        $builder->_where = $this->_where;
        $builder->_values = $this->_values;

        $builder->_limit = $this->_limit;
        $builder->_skip = $this->_skip;
        $builder->_isLockField = $this->_isLockField;

        return $builder;
    }

    // region build WhereItem STATIC
    public static function InOperate($field, $value)
    {
        return new WhereItem($field, new InOperate($value));
    }

    public static function NotInOperate($field, $value)
    {
        return new WhereItem($field, new  NotInOperate($value));
    }

    public static function EqOperate($field, $value)
    {
        return new WhereItem($field, new  EqOperate($value));
    }

    public static function NeqOperate($field, $value)
    {
        return new WhereItem($field, new  NeqOperate($value));
    }

    public static function LtOperate($field, $value)
    {
        return new WhereItem($field, new  LtOperate($value));
    }

    public static function GtOperate($field, $value)
    {
        return new WhereItem($field, new  GtOperate($value));
    }

    public static function EltOperate($field, $value)
    {
        return new WhereItem($field, new  EltOperate($value));
    }

    public static function EgtOperate($field, $value)
    {
        return new WhereItem($field, new  EgtOperate($value));
    }

    public static function BetweenOperate($field, $value, $otherValue = null)
    {
        return new WhereItem($field, new  BetweenOperate($value, $otherValue));
    }

    public static function NotBetweenOperate($field, $value)
    {
        return new WhereItem($field, new  NotBetweenOperate($value));
    }

    public static function LikeOperate($field, $value)
    {
        return new WhereItem($field, new  LikeOperate($value));
    }

    public static function NotLikeOperate($field, $value)
    {
        return new WhereItem($field, new  NotLikeOperate($value));
    }

    public static function IsNullOperate($field)
    {
        return new WhereItem($field, new  IsNullOperate());
    }

    public static function IsNotNullOperate($field)
    {
        return new WhereItem($field, new  IsNotNullOperate());
    }
    // endregion
}