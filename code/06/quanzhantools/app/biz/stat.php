<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class stat extends DCore_BackApp
{

    protected $ptype;
    protected $starttime;
    protected $endtime;

    protected function getParameter()
    {
        $this->ptype = $this->getParam("ptype");
        if (!$this->ptype)
        {
            $this->ptype = DGroups_Const::GROUP_PRODUCT_COMMON;
        }
        global $argc;
        if ($argc == 3)
        {
            $this->starttime = strtotime(date("Y-m-d 00:00:00", strtotime($this->_date)));
        }
        else
        {
            $hour = intval(date("H"));
            if ($hour == 0)
            {
                $this->starttime = strtotime(date("Y-m-d 00:00:00", strtotime("-1 day")));
                
            }
            else
            {
                $this->starttime = strtotime(date("Y-m-d 00:00:00")); 
            }
        }
        $this->endtime = $this->starttime + 86400;
    }

    public function main()
    {
        //群组名称	
        //群组创建人	
        //话题总数	
        //加精话题数	
        //活动公告贴数	
        //锁帖数	
        //贴文被顶总数
        //从 s_group_topic_


        $admindb = new DAdmin_FuncDB($this->ptype);
        $starttime = $this->starttime;
        $endtime = $this->endtime;
        $data = array();
        $titlecount = $admindb->getTopicTitleCount($starttime, $endtime);
        foreach ($titlecount["data"] as $item)
        {
            $data["data"][$item['gid']]["titlecount"] = $item['count'];
        }
        $data["titlesum"]  = $titlecount["sum"];
          //print_r($titlecount);
        if (!$titlecount)
        {
            $contentcount = $admindb->getTopicCount($starttime, $endtime);
        }
        else
        {
            $contentcount = $admindb->getReplyCount($starttime, $endtime);
        }
        foreach ($contentcount["data"] as $item)
        {
            $data["data"][$item['gid']]["contentcount"] = $item['count'];
        }
        $data["contentsum"]  = $contentcount["sum"];
        $topcount = $admindb->getTopicTopCount($starttime, $endtime);
        foreach ($topcount["data"] as $item)
        {
            $data["data"][$item['gid']]["topcount"] = $item['count'];
        }
        $data["topsum"]  = $topcount["sum"];
        $actcount = $admindb->getTopicActCount($starttime, $endtime);
        foreach ($actcount["data"] as $item)
        {
            $data["data"][$item['gid']]["actcount"] = $item['count'];
        }
        $data["actsum"]  = $actcount["sum"];
        $reccount = $admindb->getTopicRecCount($starttime, $endtime);
        foreach ($reccount["data"] as $item)
        {
            $data["data"][$item['gid']]["reccount"] = $item['count'];
        }
        $data["recsum"]  = $reccount["sum"];
        $lockcount = $admindb->getTopicLockCount($starttime, $endtime);
        foreach ($lockcount["data"] as $item)
        {
            $data["data"][$item['gid']]["lockcount"] = $item['count'];
        }
        $data["locksum"]  = $lockcount["sum"];
        
        $likecount = $admindb->getTopicLikeCount($starttime, $endtime);
        foreach ($likecount["data"] as $item)
        {
            $data["data"][$item['gid']]["likecount"] = $item['count'];
        }
        $data["likesum"]  = $likecount["sum"];
        $dbdata = array();
        
        foreach($data["data"] as $gid=>$item)
        {
            $dbdata = array();
            $dbdata = array(
                "gid" =>$gid,
                "topicnum" => isset($item['titlecount'])?$item['titlecount']:0,
                "acttopicnum" =>  isset($item['actcount'])?$item['actcount']:0,
                "locktopicnum" => isset($item['lockcount'])?$item['lockcount']:0,
                "uptopicnum" => isset($item['topcount'])?$item['topcount']:0,
                "rectopicnum" =>  isset($item['reccount'])?$item['reccount']:0,
                "liketopicnum" => isset($item['likecount'])?$item['likecount']:0,
                "statdate" => $this->starttime
            );
            $admindb->insertStatData($dbdata);
            print_r($dbdata);
        }
        
        //print_r($data);
    }

}

$stat = new stat();
$stat->run();
?>
