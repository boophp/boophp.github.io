<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class mig extends DCore_BackApp
{

    protected function main()
    {
        $ptype = DGroups_Const::GROUP_PRODUCT_CLUB;
        $admin_db = new DAdmin_FuncDB($ptype);

        $start = 0;
        $num = 1000000;
        $total = 0;
        $starttime = strtotime("-4 year");
        $endtime = time();
        $threadlist = $admin_db->getThreadListByTime($start, $num, $total, $starttime, $endtime);
        echo $total . "\n";
        $replylist = $admin_db->getReplyListByTime($start, $num, $total, $starttime, $endtime);
        echo $total . "\n";

        $topic_api = new DApi_Topic($ptype);
        $topic_func = new DFunc_Topic($ptype);
        $topic_db = new DGroups_TopicDB($ptype);

        foreach ($threadlist as $item)
        {
            try
            {
                $tid = $item['tid'];
                $gid = $item['gid'];
                $uid = $item['uid'];
                $arr = array(
                    "uid" => $uid,
                    "gid" => $gid,
                    "tid" => $tid
                );
                //    print_r($arr);
                $topic_db->addUserGroupTopic($uid, $arr);
            }
            catch (Exception $ex)
            {
                //  print_r($ex);
            }
        }
        foreach ($replylist as $item)
        {

            $tid = $item['tid'];
            $gid = $item['gid'];
            $uid = $item['uid'];

            $thread_tid = $item['thread_tid'];
            echo $tid . "\t" . $gid . "\t" . $uid . "\t" . $thread_tid . "\n";
            try
            {
                $topic_func->addUserGroupReplyList($uid, $gid, $tid, $thread_tid);
            }
            catch (Exception $ex)
            {
                //  print_r($ex);
            }
            try
            {
                $topic_func->addUserGroupReply($uid, $gid, $tid);
            }
            catch (Exception $ex)
            {
                //  print_r($ex);
            }
        }
    }

}

$mig = new mig();
$mig->run();
?>
