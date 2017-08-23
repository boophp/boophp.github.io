<?php
include_once("../../conf/global.php");

class repair extends DCore_BackApp
{
    public function main()
    {
        $where = "set mtime=ctime where thread_tid>0 AND mtime=0";
        $topicDb = new DGroups_TopicDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $topicDb->updateData($where);
    }

}

$repair = new repair();
$repair->run();
