<?php
include_once("../../conf/global.php");

class sync extends DCore_BackApp
{
    public function main()
    {
        $topicDb = new DGroups_TopicTitleDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $topicDb->syncUserTopic();
    }

}

$sync = new sync();
$sync->run();
