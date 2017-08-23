<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class migrate extends DCore_BackApp
{

    protected $type;
    static $typemap = array(
        "user" => 1,
        "idmap" => 1,
        "thread" => 1,
        "gidmap" => 1,
		"quninfo" => 1,
    );
    protected $gidfile;

    protected function getParameter()
    {
        $this->type = $this->getParam("type");
        if ($this->type == "gidmap" || $this->type == "quninfo")
        {
            $this->gidfile = $this->getParam("gidfile");
        }
    }

    protected function printUsage()
    {
        global $argc, $argv;
        echo "Usage: php " . $argv[0] . " type=[user|idmap|gidmap|thread][quninfo] [gidfile=...]\n";
    }

    protected function checkParameter()
    {
        if (!$this->type || !isset(self::$typemap[$this->type]))
        {
            $this->printUsage();
            exit;
        }
    }

    private static function insert($arr, $table)
    {
        $sql = DDb_Sql::insert($arr, $table);
        $sql = str_replace("INSERT INTO", "INSERT IGNORE INTO", $sql);
        return $sql;
    }

     
  
    public function dumpUserInfo($conn)
    {
        $ret = $conn->getData("show tables");

        $tables = array();
        foreach ($ret as $item)
        {
            $tables[] = $item['Tables_in_clubqun_audit'];
        }
        $userids = array();
        foreach ($tables as $table)
        {
            if(strpos($table, "art_body")!==false)
            {
                continue;
            }
            if(strpos($table, "art_")===false)
            {
                continue;
            }
            $sql = "SELECT cn,nickname FROM " . $table . "\n";
            $ret = $conn->getData($sql);
            foreach ($ret as $item)
            {
                list($email, $uid) = explode("|", $item['cn']);
                $userids[$email] = array("uid" => $uid, "nickname" => $item['nickname']);
            }
            //print_r($ret);
        }
        unset($tables);
        $fd = fopen("now_club_member_emails.txt", "wb");
        foreach ($userids as $email => $item)
        {
            fwrite($fd, $email . "\t" . $item['uid'] . "\t" . $item['nickname'] . "\n");
            //fwrite($fd, )
        }
        fclose($fd);
        //print_r($tables);
    }
   

    public function dumpThread($conn)
    {
        $users = array();
        $fdi = fopen("now_club_member_emails.txt", "rb");
        while ($line = fgets($fdi))
        {
            $line = trim($line);
            list($email, $uid, $username) = explode("\t", $line);
            $users[$email] = $uid;
        }
        fclose($fdi);

        $tidmap = array();
        $fdi = fopen("club_tid_map.txt", "rb");
        while ($line = fgets($fdi))
        {
            $line = trim($line);
            list($tid, $newtid) = explode("\t", $line);
            $tidmap[$tid] = $newtid;
        }
        fclose($fdi);

        $tables = array();
        $ret = $conn->getData("show tables");
        //  print_r($ret);
        foreach ($ret as $item)
        {
            $tables[] = $item['Tables_in_clubqun_audit'];
        }
        $userids = array();
        $fdos = array();
        $fdots = array();
        $fdors = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++)
        {
            $fdos[$i] = fopen("cg_clubqun_topic_title_" . $i . ".sql", "wb");
            $fdots[$i] = fopen("cg_clubqun_topic_top_" . $i . ".sql", "wb");
            $fdors[$i] = fopen("cg_clubqun_topic_rec_" . $i . ".sql", "wb");
        }
        $floormap = array();

        $fdo = fopen("club_thread_relation.txt", "wb");
        //   print_r($tables);
        foreach ($tables as $table)
        {
            $sql = "SELECT * FROM " . $table . "\n";
            $parts = explode("_", $table);
            if (count($parts) != 2)
            {
                continue;
            }
            list($prefix, $groupid) = $parts;
            if ($prefix != "art")
            {
                continue;
            }
            //  echo $sql;
            $ret = $conn->getData($sql);
            foreach ($ret as $item)
            {
                $isdeleted = $item['isdeleted'];
                if ($isdeleted)
                {
                    continue;
                }
                //print_r($item);
                //转换
                list($email) = explode("|", $item['cn']);
                $uid = $users[$email];
                if (!$uid || !$groupid)
                {
                    // print_r($item);
                    continue;
                }

                $ctime = $item['inputdate'];
                $title = $item['title'];
                $lastreplytime = $item['lastredate'];
                $lastreplyuid = $users[$item['lastrecn']];
                $replynum = $item['allchildnum'];
                $viewnum = $item['cnt'];

                $artid = $tidmap[$groupid . "_" . $item['artid']];
                $thread_tid = $tidmap[$groupid . "_" . $item['ancestorid']];
                $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                $arr = array(
                    "gid" => $groupid,
                    "tid" => $artid,
                    "ctime" => $ctime
                );
              //  print_r($arr);

                $istop = $item['istop'];
                $iselite = $item['iselite'];
                $islock = $item['islock'];

                $adminflag = 0;
                if ($istop)
                {
                    $adminflag = DUtil_Binary::set($adminflag, DGroups_Const::GROUP_TOPIC_ADMIN_FLAG_TOP, 1);
                    fwrite($fdots[$index], self::insert($arr, "cg_clubqun_topic_top_" . $index) . ";\n");
                }

                if ($iselite)
                {
                    $adminflag = DUtil_Binary::set($adminflag, DGroups_Const::GROUP_TOPIC_ADMIN_FLAG_REC, 1);
                    fwrite($fdors[$index], self::insert($arr, "cg_clubqun_topic_rec_" . $index) . ";\n");
                }

                if ($islock)
                {
                    $adminflag = DUtil_Binary::set($adminflag, DGroups_Const::GROUP_TOPIC_ADMIN_FLAG_LOCK, 1);
                }
                //   echo "$istop\t$iselite\t$islock\t$adminflag\n";
                //    print_r($item);
                //     echo $thread_tid."\n";

                $arr = array(
                    "uid" => $uid,
                    "tid" => $artid,
                    "gid" => $groupid,
                    "ctime" => $ctime,
                    "lastreplyuid" => $lastreplyuid,
                    "lastreplytime" => $lastreplytime,
                    "replynum" => $replynum,
                    "viewnum" => $viewnum,
                    "title" => $title,
                    "adminflag" => $adminflag
                    // "flag" => $
                );

                $floor = 0;
                if ($thread_tid != $artid)
                {
                    $floor = $floormap[$thread_tid]++;
                }
                //回帖不入标题库
                if ($thread_tid == $artid)
                {
                    fwrite($fdos[$index], self::insert($arr, "cg_clubqun_topic_title_" . $index) . ";\n");
                }
                if ($thread_tid == $artid)
                {
                    $floor = 0;
                    $thread_tid = 0;
                }
                fwrite($fdo, $artid . "\t" . $uid . "\t" . $isdeleted . "\t" . $floor . "\t" . $ctime . "\t" . $groupid . "\t" . $thread_tid . "\n");
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++)
        {
            fclose($fdos[$i]);
            fclose($fdots[$i]);
            fclose($fdors[$i]);
        }
    }

    public function buildTidMap($conn)
    {
        $ret = $conn->getData("show tables");

        $tables = array();

        foreach ($ret as $item)
        {
            $tables[] = $item['Tables_in_clubqun_audit'];
        }

        $fdo = fopen("club_tid_map.txt", "wb");
        $ret = array();
        foreach ($tables as $table)
        {
            $sql = "SELECT  artid FROM " . $table . "\n";
            //echo $sql;
            $parts = explode("_", $table);
            if (count($parts) != 2)
            {
                continue;
            }
            list($prefix, $groupid) = $parts;
            if ($prefix != "art")
            {
                continue;
            }
            $tmp = $conn->getData($sql);
            foreach ($tmp as &$item)
            {
                $item['gid'] = $groupid;
            }
            $ret = array_merge($ret, $tmp);
        }
        unset($tables);

        foreach ($ret as $item)
        {
            $new_tid = DDb_IDGen::getNewId(DDb_Const::GROUP_TOPIC_TID_KEY);
            $artid = $item['gid'] . "_" . $item['artid'];
            fwrite($fdo, $artid . "\t" . $new_tid . "\n");
        }
        fclose($fdo);

        DDb_DBConfig::closeInstance();
    }

    public function dumpThreadReply($conn)
    {
        $ret = $conn->getData("show tables");
        $tidmap = array();
        $fdi = fopen("club_tid_map.txt", "rb");
        while ($line = fgets($fdi))
        {
            $line = trim($line);
            list($tid, $newtid) = explode("\t", $line);
            $tidmap[$tid] = $newtid;
        }
        fclose($fdi);

        $artmap = array();
        $fdi = fopen("club_thread_relation.txt", "rb");
        while ($line = fgets($fdi))
        {
            $line = trim($line);
            list($artid, $uid, $isdeleted, $floor, $ctime, $groupid, $thread_tid) = explode("\t", $line);
            $artmap[$artid] = array(
                "thread_tid" => $thread_tid,
                "floor" => $floor,
                "isdeleted" => $isdeleted,
                "uid" => $uid,
                "ctime" => $ctime);
        }
        fclose($fdi);


        $tables = array();

        foreach ($ret as $item)
        {
            $tables[] = $item['Tables_in_clubqun_audit'];
        }
        $userids = array();
        $fdos = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++)
        {
            $fdos[$i] = fopen("cg_clubqun_topic_" . $i . ".sql", "wb");
        }

        foreach ($tables as $table)
        {
            $sql = "SELECT * FROM " . $table . "\n";
            $parts = explode("_", $table);
            if (count($parts) != 3)
            {
                continue;
            }
            list($prefix, $prefix2, $groupid) = $parts;
            if ($prefix != "art")
            {
                continue;
            }

            $ret = $conn->getData($sql);

            foreach ($ret as $item)
            {
                //   print_r($item);
                //转换
                $tid = $tidmap[$groupid . "_" . $item['artid']];
                //echo $tid."\n";
                $content = $item['body'];
                $thread_tid = 0;
                $floor = 0;
                $flag = 0;
                $uid = 0;
                $ctime = 0;
                if (isset($artmap[$tid]))
                {
                    //print_r($artmap[$tid]);
                    $subitem = $artmap[$tid];
                    $thread_tid = $subitem["thread_tid"];
                    $uid = $subitem["uid"];
                    $isdeleted = $subitem['isdeleted'];
                    $floor = $subitem['floor'];
                    if ($isdeleted)
                    {
                        $flag = DGroups_Const::GROUP_AUDIT_STATUS_DEL;
                    }
                    $ctime = $subitem['ctime'];
                }
                else
                {
                    //    echo "not set ".$tid." "."! \n";
                }
                //     error_reporting(E_ALL);
                //    ini_set("display_errors", 1);
                if ($tid == $thread_tid)
                {
                    $thread_tid = 0;
                }
                $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                $arr = array(
                    "uid" => $uid,
                    "tid" => $tid,
                    "thread_tid" => $thread_tid,
                    "content" =>  DCntv_UBBHelper::decodeClub($content, $groupid, $item['artid']),
                    "gid" => $groupid,
                    "ctime" => $ctime,
                    "flag" => $flag,
                    "floor" => $floor
                );
                // print_r($arr);
                if (!$uid || !$groupid)
                {
                    // print_r($item);
                    continue;
                }
                fwrite($fdos[$index], self::insert($arr, "cg_clubqun_topic_" . $index) . ";\n");
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++)
        {
            fclose($fdos[$i]);
        }
    }

    public function main()
    {
        global $CONFS;
        $config = $CONFS['db'];
        $host = $config['host']['m'];
        $user = $config['user'];
        $passwd = $config['pass'];
        $port = $config['port'];
        $dbs = array(
            "clubqun_audit",
            //       "group_base",
            //      "group_db"
        );

        switch ($this->type)
        {
            case "user":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[0]);
                echo "开始导出用户信息\n";
                $this->dumpUserInfo($conn);
                echo "开始从用户表导出成员信息\n";
                //            $this->dumpMemberInfoByUser($conn);
                /* $ret = $conn->getData("show tables");
                  print_r($ret); */
                break;
            //print_r($ret);
            case "idmap":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[0]);
                $this->buildTidMap($conn);
                break;
            case "thread":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[0]);
                echo "开始导出群组帖子标题信息\n";
                $this->dumpThread($conn);
                echo "开始导出群组帖子内容信息\n";
                $this->dumpThreadReply($conn);
                //print_r($ret);
//print_r($CONFS['db']); 
                break;
            case "gidmap":
                $gidfile = $this->gidfile;
                $fdi = fopen($gidfile, "rb");
                $fdo = fopen("cg_club_qun.sql", "wb");
                while ($line = fgets($fdi))
                {
                    list($uid, $gid) = explode("\t", trim($line));
                    $sql = "INSERT INTO cg_club_qun(clubid, gid) VALUES($uid, $gid);\n";
                    fwrite($fdo, $sql);
                }
                fclose($fdi);
                fclose($fdo);
                break;
                case "quninfo":
	$quninfo = $this->gidfile;
                $fdi = fopen($quninfo, "rb");
                $fdo = fopen("cg_clubqun_info.sql", "wb");
                while ($line = fgets($fdi))
                {
                    list($gid, $gname, $uid) = explode("\t", trim($line));
					$time = time();
                    $sql = "INSERT INTO cg_clubqun_info(gid, gname, cuid, ctime) VALUES($gid, '$gname', $uid, $time);\n";
                    fwrite($fdo, $sql);
                }
                fclose($fdi);
                fclose($fdo);
                break;

        }
    }

}

$migrate = new migrate();
$migrate->run();
?>
