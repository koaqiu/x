<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 20:49
 */

namespace x\Db\Operates;


use x\Db\Dbase;

class WhereItem
{
    public $field;
    public $value;

    function __construct($field, WhereOperate $value)
    {
        $this->value = $value;
        $this->field = $field;
    }

    public function build()
    {
        return "`"
        . Dbase::get()->EscapeString($this->field)
        . "`" . $this->value->build();
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