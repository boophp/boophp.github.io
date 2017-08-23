<?php
/**
 * 旧数据中，修复（cg_clubqun_topic_title_0）表中，有些数据有最后回复时间，但没有最后回复人
 */
include_once("../../conf/global.php");

class repair extends DCore_BackApp
{
    protected function getParameter() {
        $this->tablenum = $this->getParam("num", 4);
    }

    public function main()
    {
        $topicTitle = new DGroups_TopicTitleDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $topicDB = new DGroups_TopicDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        for ($i = 0; $i < $this->tablenum; $i++) {
            for ($start = 0; $start < 10000; $start += 1000) {
                $topicList = $topicTitle->getAllTopicTitle($i, $start, 1000);
                foreach($topicList as $topic) {
                    if ($topic['lastreplytime'] && empty($topic['lastreplyuid'])) {
                        $uid = $topicDB->getLastReplyUid($topic['gid'], $topic['tid']);
                        if ($uid) {
                            $topicTitle->updateLastReplyUid($topic['gid'], $topic['tid'], $uid);
                        }
                    }
                }
            }
           
        }
    }
}

$repair = new repair();
$repair->run();
