<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 17:01
 */

namespace x\Db;

use x\App;
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
use x\Utils\xString;

class Dbase
{
    private static $_inc = array();
    private $_db;
    private $_table_prefix = '';
    private $_lastSql = false;
    private function __construct(array $dbConfig = null)
    {
        if(isset($dbConfig["prefix"])){
            $this->setTable($dbConfig["prefix"]);
        }
        if(!class_exists('mysqli')){
            throw new \ErrorException("找不到数据库组件：mysqli");
        }
        $this->_db = new \mysqli(
            $dbConfig["host"],
            $dbConfig["user"],
            $dbConfig["pass"],
            $dbConfig["dbName"],
            $dbConfig["port"]
        );
	    $this->_db->set_charset("utf8");
    }

    public static function get(array $dbConfig = null){
	    if ($dbConfig == null) {
		    $dbConfig = App::getConfig()['db'];
	    }
	    $key = md5(json_encode($dbConfig));

        if(!array_key_exists($key, self::$_inc)){
            self::$_inc[$key] = new Dbase($dbConfig);
        }
        return self::$_inc[$key];
    }
    public function countSqlBuilder($tableName){
    	$builder = SqlBuilder::Select($this, $tableName);
	    $builder->fields("COUNT(*)");
	    $builder->lockField();
        return $builder;
    }
    public function createSqlBuilder($tableName, $action = SqlBuilder::ACTION_SELECT, $tablePrefix = false){
        $tableName = $this->getTableName($tableName, $tablePrefix);
        switch ($action){
            case SqlBuilder::ACTION_SELECT:
                return SqlBuilder::Select($this, $tableName);
            case SqlBuilder::ACTION_INSERT:
                return SqlBuilder::Insert($this, $tableName);
            case SqlBuilder::ACTION_UPDATE:
                return SqlBuilder::Update($this, $tableName);
            case SqlBuilder::ACTION_DELETE:
                return SqlBuilder::Delete($this, $tableName);
        }
    }
    public function getLastSql(){
        return $this->_lastSql;
    }

    public function execute($sql){
        $this->_lastSql = $sql;
        $result = $this->_db->real_query($sql);
        if($result){
            if(stripos($sql,'INSERT INTO') !== false){
                return $this->_db->insert_id;
            }
//            if(stripos($sql,'UPDATE ') !== false){
//                return $this->_db->field_count;
//            }
//            if(stripos($sql,'DELETE ') !== false){
//                return $this->_db->field_count;
//            }
            $result2 = $this->_db->store_result();
            if($result2)
                return $result2;
            else
                return $result;
        }
        return $result;
    }
    public function query($sql){
        $this->_lastSql = $sql;
        $q = $this->_db->query($sql);
        if(!$q)return false;
        try {
            if(method_exists($q,'fetch_all')){
                $result = $q->fetch_all(MYSQLI_ASSOC);
            }else{
                while ($row = $q->fetch_assoc()) {
                    $result[]=$row;
                }
            }
        }catch (\ErrorException $exception){
        }
        $q->close();
        return $result;
    }
    public function firstLine($sql){
        $this->_lastSql = $sql;
        $q = $this->_db->query($sql);
        if($q) {
            try {
                $result = $q->fetch_assoc();
            } catch (\ErrorException $exception) {
            }
            $q->close();
            return $result;
        }
        return false;
    }
    protected function getTableName($tableName, $prefix = false){
        if($prefix){
            return $prefix . xString::LeftTrim ($tableName, $prefix);
        }
        if(empty($this->_table_prefix))return $tableName;
        return $this->_table_prefix . xString::LeftTrim ($tableName, $this->_table_prefix);
    }
    public function setTable($prefix){
        if($prefix && !empty($prefix))
            $this->_table_prefix = $prefix;
    }
    public function EscapeString($str, $addQuotation = false){
        if($addQuotation && strpos($str, "`") === false) {
	        return "`" . $this->_db->real_escape_string($str) . "`";
        }
        return $this->_db->real_escape_string($str);
    }

    function __destruct()
    {
        $this->_db->close();
    }

    //region where 操作
    public static function InOperate($value){
        return new InOperate($value);
    }
    public static function NotInOperate($value){
        return new NotInOperate($value);
    }

    public static function EqOperate($value){
        return new EqOperate($value);
    }
    public static function NeqOperate($value){
        return new NeqOperate($value);
    }
    public static function LtOperate($value){
        return new LtOperate($value);
    }
    public static function GtOperate($value){
        return new GtOperate($value);
    }
    public static function EltOperate($value){
        return new EltOperate($value);
    }
    public static function EgtOperate($value){
        return new EgtOperate($value);
    }

    public static function BetweenOperate($value){
        return new BetweenOperate(func_get_args());
    }
    public static function NotBetweenOperate($value){
        return new NotBetweenOperate($value);
    }
    public static function LikeOperate($value){
        return new LikeOperate($value);
    }
    public static function NotLikeOperate($value){
        return new NotLikeOperate($value);
    }
    public static function IsNullOperate(){
        return new IsNullOperate();
    }
    public static function IsNotNullOperate(){
        return new IsNotNullOperate();
    }
    //endregion
}