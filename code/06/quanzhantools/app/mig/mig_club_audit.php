<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once("../../conf/global.php");

class mig extends DCore_BackApp
{

    private function mig_group_info()
    {

        global $CONFS;
        $config = $CONFS['db'];
        $host = $config['host']['m'];
        $user = $config['user'];
        $passwd = $config['pass'];
        $port = $config['port'];
        $dbname = "group_audit";
        $newdbname = "group_audit";
        //print_r($config);

        $conn = new DDb_Handle($host, $port, $user, $passwd, $newdbname);
        $sql = "SELECT * FROM cg_group_content_history_0 WHERE type='" . DAudit_Const::GROUP_AUDIT_TYPE_INFO . "' LIMIT 1";

        $data = $conn->getData($sql);
        print_r(unserialize($data[0]['content']));

        $conn->closeDb();

        $conn = new DDb_Handle($host, $port, $user, $passwd, $dbname);
        $sql = "SELECT * FROM sync_group";
        echo $sql;

        $groups = $conn->getData($sql);


        $audit_handle = new DApi_Audit(DGroups_Const::GROUP_PRODUCT_COMMON);
        $api_handle = new DApi_Info(DGroups_Const::GROUP_PRODUCT_COMMON);
        foreach ($groups as $item)
        {
            $gid = $item['groupid'];
            $group = $api_handle->getSingleGroup($gid);
            $arr = array(
                "gid" => $group['gid'],
                "gname" => $group['gname'],
                "intro" => $group['intro'],
                "logo" => $group['logo'],
                "cuid" => $group['cuid'],
                "catid" => $group['cid'],
                "privacy" => $group['privacy'],
                "nickname" => $item['updatecn'],
                "nowip" => "",
                "ctime" => $group['ctime'],
                "result" => "",
                "question" => "",
                "reason" => "",
                "updatetime" => $item['updatetime'],
                "synctime" => $item['synctime'],
                "sync" => $item['sync'],
                "result" => $item['result']
            );
            $audit_handle->addGroupContentHistory($gid, $gid, 0, DAudit_Const::GROUP_AUDIT_TYPE_INFO, $arr);
            $status = DGroups_Const::GROUP_AUDIT_STATUS_PASS;
            if ($item['result'] == 0)
            {
                $status = DGroups_Const::GROUP_AUDIT_STATUS_WAIT;
            }
            else if ($item['result'] == 2)
            {
                $status = DGroups_Const::GROUP_AUDIT_STATUS_DEL;
            }
            $audit_handle->addAuditDoneContent($gid, $gid . "_" . "0", DAudit_Const::GROUP_AUDIT_TYPE_INFO, $arr, 1, "", "system", "", $status);
            echo "process " . $gid . "\n";
//print_r($group);
        }
        $conn->closeDb();
        /*

          id: 10
          groupid: 10010
          groupname: 美国国家足球队
          description: 美国国家足球队——世界杯C组。
          logo: 20100527/1274959769189.jpg
          tags:
          updatetime: 1274959769
          channelid: 2
          sync: 2
          synctime: 1274959786
          result: 1
          change:
          updatecn: luby0000@tom.com
          updateuid: 0 */
        /* [gid] => 4
          [gname] => 数学学习群
          [intro] => 数学学习群
          [logo] => /logo/temp/13516137634969/201210/c3de05f8d76e6fd0.png
          [privacy] => 2
          [cuid] => 17995782
          [catid] => 4
          [nickname] => wxstars
          [nowip] => 111.161.17.89
          [ctime] => 1351613820
          [result] =>
          [question] =>
          [reason] => */



        /* $conn = mysql_connect($host . ":" . $port, $user, $passwd);
          if (mysql_select_db($dbname))
          {


          $sql = "SELECT * FROM cg_group_audit_history_0 WHERE type='" . DAudit_Const::GROUP_AUDIT_TYPE_INFO . "' LIMIT 1";

          //$data = $conn->runSql($sql);
          $result = mysql_query($sql);
          if ($data = mysql_fetch_array($result, MYSQLI_ASSOC))
          {
          print_r($data);
          }
          $sql = "SELECT * FROM sync_group";
          echo $sql;
          }
          else
          {
          echo mysql_error();
          } */
    }

    protected function main()
    {
        global $CONFS;
        $config = $CONFS['db'];
        $host = $config['host']['m'];
        $user = $config['user'];
        $passwd = $config['pass'];
        $port = $config['port'];
        $dbname = "clubqun_audit";
        $newdbname = "clubqun_audit";

        $conn = new DDb_Handle($host, $port, $user, $passwd, $newdbname);
        $sql = "SELECT * FROM cg_group_content_history_0 WHERE type='" . DAudit_Const::GROUP_AUDIT_TYPE_CLUBQUN_TOPIC . "' LIMIT 1";

        $data = $conn->getData($sql);
        print_r(unserialize($data[0]['content']));

        $conn->closeDb();

        $filename = "club_tid_map.txt";
        $lines = file($filename);
        $map = array();
        foreach ($lines as $line)
        {
            list($oldid, $newid) = preg_split("#\s+#", $line);
            $map[$oldid] = $newid;
        }
        $conn = new DDb_Handle($host, $port, $user, $passwd, $dbname);

        $months = array(
            "201107",
            "201108",
            "201109",
            "201110",
            "201111",
            "201112",
            "201201",
            "201202",
            "201203",
            "201204",
            "201205",
            "201206",
            "201207",
            "201208",
            "201209",
            "201210",
            "201211",
            "201212",
        );

        foreach ($months as $month)
        {
            $sql = "SELECT * FROM club_art_".$month;
            echo $sql."\n";

            $topics = $conn->getData($sql);

            $sql = "SELECT * FROM artreply_".$month;
            echo $sql."\n";

            $topics1 = $conn->getData($sql);
            
            $topics = array_merge($topics, $topics1);

            $audit_handle = new DApi_Audit(DGroups_Const::GROUP_PRODUCT_CLUB);
            $topic_handle = new DApi_Topic(DGroups_Const::GROUP_PRODUCT_CLUB);
            /*    id: 10
              artid: 32
              ancestorid: 2
              title:
              body: [emote]6[/emote][emote]10[/emote][emote]13[/emote]
              body1:
              updatetime: 1309786320
              groupid: 10005
              groupname: CNTVQA俱乐部
              clubusername: cakee013@cntv.cn
              channelid: 0
              result: 2
              updatecn: ply0901@163.com
              updateuid: 9302926
              audittype: 0
              auditorid: 66
              auditor: QA - 彭小丽
              audittime: 1309787101
              usertype: 0
              changefields: */
            /**
            
             */
             
            foreach ($topics as $topic)
            {
                $gid = $topic['groupid'];
                $oldid = $gid . "_" . $topic['artid'];
                $newid = $map[$oldid];
                $newthread_id = $map[$gid . "_" . $topic['ancestorid']];
                if ($newid == $newthread_id)
                {
                    $info = $topic_handle->getSingleTopic($gid, $newid);
                }
                else
                {
                    $info = $topic_handle->getSingleReplyInfo($gid, $newid);
                }
                $content = $info['content'];
                if (!$content)
                {
                    $content = DCntv_UBBHelper::decodeClub($topic['body'], $gid, $topic['artid']);
                }
                //  print_r($info);
                $arr = array(
                    "gid" => $topic['groupid'],
                    "tid" => $newid,
                    "uid" => $info['uid'],
                    "title" => $info['title'],
                    "content" => $content,
                    "islock" => $info['lock'],
                    "ctime" => $topic['updatetime'],
                    "userip" => "",
                    "keywords" => "",
                    
                    "updatecn" => $topic['updatecn'],
                    "updatetime" => $topic['updatetime'],
                    "synctime" => $topic['synctime'],
                    "sync" => $topic['sync'],
                    "result" => $topic['result'],
                    "title1" => $topic['title1'],
                    "body1" => $topic['body1'],
                    "inputtime" => $topic['inputtype'],
                    "groupname" => $topic['groupname'],
                    "clubusername" => $topic['clubusername'],
                    "channdelid" => $topic['channelid'],
                    "audittype" => $topic['audittype'],
                    "updateuid" => $topic['updateuid'],
                    "nickname" => $topic['nickname'],
                    "published" => $topic['published'],
                    "auditorid" => $topic['auditorid'],
                    "auditor" => $topic['auditor'],
                    "audittime" => $topic['audittime'],
                    "usertype" => $topic['usertype'],
                    "changefield" => $topic['changefield']
                );
                
                  
             /* `updateuid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发表或修改用户的ID',
              `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
              `published` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否先发后审',
              `auditorid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '审核人ID',
              `auditor` varchar(60) NOT NULL DEFAULT '' COMMENT '审核人帐号',
              `audittime` int(11) NOT NULL DEFAULT '0' COMMENT '审核时间',
              `usertype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '发贴人类型1俱乐部0个人',
              `changefield` varchar(50) NOT NULL DEFAULT '' COMMENT '修改的字段',*/
                if ($newid != $newthread_id)
                {
                    $arr["thread_tid"] = $newthread_id;
                }
                $audit_handle->addGroupContentHistory($gid, $newid, $newthread_id, DAudit_Const::GROUP_AUDIT_TYPE_CLUBQUN_TOPIC, $arr);
                $status = DGroups_Const::GROUP_AUDIT_STATUS_PASS;
                if ($topic['result'] == 0)
                {
                    $status = DGroups_Const::GROUP_AUDIT_STATUS_WAIT;
                }
                else if ($topic['result'] == 2)
                {
                    $status = DGroups_Const::GROUP_AUDIT_STATUS_DEL;
                }
                $audit_handle->addAuditDoneContent($gid, $newid . "_" . $newthread_id, DAudit_Const::GROUP_AUDIT_TYPE_CLUBQUN_TOPIC, $arr, 1, "", "system", "", $status, $topic['audittype']);
                echo "process " . $gid . " " . $newid . " " . $newthread_id . "\n";
                // print_r($arr);
            }
        }
        $conn->closeDb();
        /*  [gid] => 4
          [tid] => 7819
          [uid] => 18915523
          [title] => wfwefwefw
          [content] => wfewfwefwfew<p></p>
          [islock] =>
          [ctime] => 1351654775
          [userip] => 220.181.126.4
          [keywords] => */
    }

}

$mig = new mig();
$mig->run();
?>
