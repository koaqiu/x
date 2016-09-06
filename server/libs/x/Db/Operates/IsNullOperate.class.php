<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:24
 */

namespace x\Db\Operates;
use x\Db\Dbase;


class IsNullOperate extends WhereOperate
{
    function __construct() {
        parent::__construct(null, "IS NULL");
    }
    public function build()
    {
        return "IS NULL";
    }
}