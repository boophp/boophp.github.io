<?php
/**
 * 旧数据中，用户回复表无数据（cg_user_clubqun_reply）
 */
include_once("../../conf/global.php");

class repair extends DCore_BackApp
{
    protected function getParameter() {
        $this->tablenum = $this->getParam("num", 4);
    }

    public function main()
    {
        $replyDb = new DGroups_ReplyDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $admin = new DAdmin_FuncDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $topicDb = new DGroups_TopicDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $replyList = $admin->getTopReplyList($start = 0, $num = 4000);
        $this->dealReply($replyList);
        for ($i = 0; $i<$this->tablenum; $i++) {
            $replyList = $topicDb->getAllReplyList($i);
            $this->dealReply($replyList);
        }
    }

    private function dealReply($replyList) {
        $replyDb = new DGroups_ReplyDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        foreach($replyList as $reply) {
                $uid = $reply['uid'];
                $gid = $reply['gid'];
                $thread_tid = $reply['thread_tid'];
                if ($replyDb->getSingleReplyTopicNum($uid, $gid, $thread_tid) == 0) {
                    $replyDb->addUserGroupReply($uid, $gid, $thread_tid);
                }
                
                $tid = $reply['tid'];
                if ($replyDb->getUserReplyedTopic($uid, $gid, $tid, $thread_tid) == 0) {
                    $replyDb->addUserGroupReplyList($uid, compact('uid', 'tid', 'gid', 'thread_tid'));
                }
            }
    }

}

$repair = new repair();
$repair->run();
