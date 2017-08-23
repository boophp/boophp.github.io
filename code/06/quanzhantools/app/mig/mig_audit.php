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
        $dbname = "group_audit";
        $newdbname = "group_audit";

        $conn = new DDb_Handle($host, $port, $user, $passwd, $newdbname);
        $sql = "SELECT * FROM cg_group_content_history_0 WHERE type='" . DAudit_Const::GROUP_AUDIT_TYPE_TOPIC . "' LIMIT 1";

        $data = $conn->getData($sql);
        print_r(unserialize($data[0]['content']));

        $conn->closeDb();

        $filename = "group_tid_map.txt";
        $lines = file($filename);
        $map = array();
        foreach ($lines as $line)
        {
            list($oldid, $newid) = preg_split("#\s+#", $line);
            $map[$oldid] = $newid;
        }
        $conn = new DDb_Handle($host, $port, $user, $passwd, $dbname);
        $sql = "SELECT * FROM sync_art";
        echo $sql;

        $topics = $conn->getData($sql);


        $audit_handle = new DApi_Audit(DGroups_Const::GROUP_PRODUCT_COMMON);
        $topic_handle = new DApi_Topic(DGroups_Const::GROUP_PRODUCT_COMMON);
        /*
         *         id: 100
          artid: 6
          ancestorid: 6
          title: 斯洛伐克队世界杯战绩
          body: 1930年年至1994年年 - 未有参加 　　
          1998年年至2006年年 - 外围赛
          updatetime: 1275120672
          groupid: 10024
          groupname: 斯洛伐克国家足球队
          channelid: 2
          sync: 2
          synctime: 1275121639
          result: 1
          change:
          updatecn: luby0000@tom.com
          updateuid: 0
          islock: 0
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
            if(!$content)
            {
                $content = DCntv_UBBHelper::decode($topic['body'], $gid, $topic['artid']);
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
                "result" => $topic['result']
            );
            if ($newid != $newthread_id)
            {
                $arr["thread_tid"] = $newthread_id;
            }
            $audit_handle->addGroupContentHistory($gid, $newid, $newthread_id, DAudit_Const::GROUP_AUDIT_TYPE_TOPIC, $arr);
            $status = DGroups_Const::GROUP_AUDIT_STATUS_PASS;
            if ($topic['result'] == 0)
            {
                $status = DGroups_Const::GROUP_AUDIT_STATUS_WAIT;
            }
            else if ($topic['result'] == 2)
            {
                $status = DGroups_Const::GROUP_AUDIT_STATUS_DEL;
            }
            $audit_handle->addAuditDoneContent($gid, $newid . "_" . $newthread_id, DAudit_Const::GROUP_AUDIT_TYPE_TOPIC, $arr, 1, "", "system", "", $status);
            echo "process " . $gid . " " . $newid . " " . $newthread_id . "\n";
            // print_r($arr);
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
