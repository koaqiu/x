<?php
use x\Api\BaseApi;
use x\Db\Operates\WhereItem;
use x\Db\SqlBuilder;
use x\Db\Dbase;

/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/2
 * Time: 15:28
 */
class test extends BaseApi
{
    function getAction(){
        return "test";
    }
    function execute()
    {
        $db = Dbase::get();
        $sql = $db->createSqlBuilder('hs_test')
            ->fields(array("hs_testcol","id"))
            ->orderBy("id desc")
            ->build();
        $data['sql'] = $sql;
        $data['sql2'] = $db->createSqlBuilder('hs_test',SqlBuilder::ACTION_INSERT)
            ->fields(array("hs_testcol","id"))
            ->values(array(
                '1+1',2
            ))
            ->build();
        $data['sql_del'] = $db->createSqlBuilder('hs_test',SqlBuilder::ACTION_DELETE)
            ->fields(array("hs_testcol","id"))
            ->where(array(
                "title" => Dbase::LikeOperate("abcd"),
                array(
                "a"=>Dbase::IsNullOperate(),
                "b2"=>Dbase::IsNotNullOperate()
                ),
                '_op_'=>2,
                'time'=>Dbase::BetweenOperate(new DateTime(),'2016-10-1'),
                WhereItem::LtOperate("id",100),
                WhereItem::GtOperate("id",10),
                'b'=> Dbase::InOperate(array(1,2,3, new DateTime()))
            ))
            ->build();
        $data['sql_update'] = $db->createSqlBuilder('hs_test',SqlBuilder::ACTION_UPDATE)
            ->fields(array("hs_testcol","id"))
            ->values(array(
                '1+1',2
            ))
            ->where(array(
                "title" => "like ",
                '_op_'=>2,
                'id' => 1,
            ))
            ->build();
        $data['sql_count'] = $db->countSqlBuilder('hs_test')
            ->build();
        //$data['data'] = $db->query($sql);
        $data['r']= $db->query($sql);
        return $this->success($data);
    }
}