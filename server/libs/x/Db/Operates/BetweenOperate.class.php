<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/3
 * Time: 16:18
 */

namespace x\Db\Operates;


class BetweenOperate extends WhereOperate
{
    function __construct($value) {
        if(is_array($value) && count($value) == 2) {
            parent::__construct($value, "Between");
        }else if(func_num_args() > 1){
            $data[] = func_get_arg(0);
            $data[] = func_get_arg(1);
            parent::__construct($data, "Between");
        }else {
            throw new \ErrorException("必须指定2个参数");
        }
    }
    public function build()
    {
        return "BETWEEN ".$this->fixValue($this->value[0])." AND ".$this->fixValue($this->value[1]);
    }
}