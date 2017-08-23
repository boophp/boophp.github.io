<?php

include_once("../../conf/global.php");

class recover extends DCore_BackApp {

    protected function getParameter() {
        $this->gid = $this->getParam("gid", 0);
    }

    protected function checkParameter() {
        if ($this->gid <= 0) {
            echo "Usage:php " . basename(__FILE__) . " gid=xxx\n";
            exit;
        }
    }

    public function main() {
        $topicDb = new DGroups_TopicDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $topicTitleDb = new DGroups_TopicTitleDB(DGroups_Const::GROUP_PRODUCT_CLUB);

        $apiTopic = new DApi_Topic(DGroups_Const::GROUP_PRODUCT_CLUB);
        $arrTopicList = $apiTopic->getTopicList($this->gid, 0, 10000);
        foreach ($arrTopicList as $arrTopic) {
            $thread_tid = $arrTopic['tid'];
            $arrTmp = $topicTitleDb->getTopicTitle($this->gid, $thread_tid);
            $replyNum = $topicDb->getTopicListCount($this->gid, $thread_tid);
            if ($arrTmp[0]['replynum'] == 0) {
                if ($replyNum > 0) {
                    $viewNum = $replyNum * 4 + rand(10, 20);
                    $topicTitleDb->recoverData($this->gid, $thread_tid, $replyNum, $viewNum);
                } else {
                    $viewNum = $replyNum * 4 + rand(1, 30);
                    $topicTitleDb->recoverData($this->gid, $thread_tid, $replyNum, $viewNum);
                }
            } else if ($arrTmp[0]['viewnum'] == 0) {
                $viewNum = $arrTmp[0]['replynum'] * 4 + rand(1, 20);
                $topicTitleDb->recoverData($this->gid, $thread_tid, $replynum = 0, $viewNum);
            } else if ($arrTmp[0]['replynum'] > $arrTmp[0]['viewnum']) {
                if ($replyNum > 0) {
                    $viewNum = $replyNum * 4 + rand(10, 20);
                    $topicTitleDb->recoverData($this->gid, $thread_tid, $replyNum, $viewNum);
                } else {
                    $viewNum = $replyNum * 4 + rand(1, 30);
                    $topicTitleDb->recoverData($this->gid, $thread_tid, $replyNum, $viewNum);
                }
            } else {
                $viewNum = $replyNum * 4 + rand(1, 30);
                $topicTitleDb->recoverData($this->gid, $thread_tid, $replyNum, $viewNum);
            }
        }
    }

}

$recover = new recover();
$recover->run();
