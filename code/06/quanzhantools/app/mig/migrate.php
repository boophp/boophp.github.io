<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class migrate extends DCore_BackApp {

    protected $type;
    static $typemap = array(
        "member" => 1,
        "group" => 1,
        "thread" => 1,
        "user" => 1,
        "idmap" => 1,
        "notice" => 1
    );

    protected function getParameter() {
        $this->type = $this->getParam("type");
    }

    protected function printUsage() {
        global $argc, $argv;
        echo "Usage: php " . $argv[0] . " type=[member|group|thread|user|idmap|notice]\n";
    }

    protected function checkParameter() {
        if (!$this->type || !isset(self::$typemap[$this->type])) {
            $this->printUsage();
            exit;
        }
    }

    private static function insert($arr, $table) {
        $sql = DDb_Sql::insert($arr, $table);
        $sql = str_replace("INSERT INTO", "INSERT IGNORE INTO", $sql);
        return $sql;
    }

    private function dumpGroupInfo($conn) {
        // echo $host;

        $ret = $conn->getData("show tables");
        $users = array();
        $fdi = fopen("cg_group_username.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($typeid, $uid, $username, $email) = explode("\t", $line);
            $users[$email] = $uid;
        }
        fclose($fdi);
        $tables = array();
        $fdo = fopen("dump_group.sql", "wb");
        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        print_r($tables);
        if (in_array("class", $tables)) {
            $ret = $conn->getData("select * from class order by sequence");
            // print_r($ret);
            foreach ($ret as $item) {
                $arr = array(
                    "cid" => $item['id'],
                    "cname" => trim($item['name']),
                    'ctime' => $item['createtime'],
                );
                fwrite($fdo, self::insert($arr, "cg_group_cate") . ";\n");
            }
        }



        if (in_array("forum", $tables)) {
            $ret = $conn->getData("select * from forum order by forumid");
            // print_r($ret);
            foreach ($ret as $item) {
                if ($item['isdeleted']) {
                    continue;
                }
                //公开
                $privacy = 0;
                if ($item['privacyopen'] == '111') {
                    $privacy = DUtil_Binary::set($privacy, DGroups_Const::GROUP_PRIVACY_TYPE_PUBLIC, 1);
                } else if ($item['privacyopen'] == '100') { //隐私
                    $privacy = DUtil_Binary::set($privacy, DGroups_Const::GROUP_PRIVACY_TYPE_PRIVATE, 1);
                } else {
                    $privacy = DUtil_Binary::set($privacy, DGroups_Const::GROUP_PRIVACY_TYPE_INVITE, 1);
                }
                if ($item['privacyjoin'] == '1') {
                    
                }
                $arr = array(
                    "gid" => $item['forumid'],
                    "gname" => trim($item['groupname']),
                    'intro' => $item["description"],
                    "logo" => $item["logoext"],
                    "cuid" => $users[$item["createuserid"]],
                    "cip" => "",
                    "ctime" => $item['createdate'],
                    "innercode" => "",
                    "cid" => $item["classid"],
                    "notice" => '',
                    "privacy" => $privacy
                );
                fwrite($fdo, self::insert($arr, "cg_group_info") . ";\n");
                $exarr = array(
                    "membercount" => $item['membercount'],
                    "gid" => $item['forumid']
                );

                fwrite($fdo, self::insert($exarr, "cg_group_ex") . ";\n");
            }
        }
        unset($tables);
        fclose($fdo);
    }

    public function dumpUserInfo($conn) {
        $ret = $conn->getData("show tables");

        $tables = array();
        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        $userids = array();
        foreach ($tables as $table) {
            if (strpos($table, "user_") === false) {
                continue;
            }
            $sql = "SELECT userid FROM " . $table . "\n";
            $ret = $conn->getData($sql);
            foreach ($ret as $item) {
                $userids[$item['userid']] = 1;
            }
            //print_r($ret);
        }
        unset($tables);
        $fd = fopen("now_member_emails.txt", "wb");
        foreach ($userids as $email => $uid) {
            fwrite($fd, $email . "\n");
            //fwrite($fd, )
        }
        fclose($fd);
        //print_r($tables);
    }

    public function dumpMemberInfoByUser($conn) {
        $ret = $conn->getData("show tables");
        $users = array();
        $fdi = fopen("cg_group_username.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($typeid, $uid, $username, $email) = explode("\t", $line);
            /* $users[$email] = array(
              "uid" =>$uid,
              "type" => $typeid,
              "name" => $username,
              "email" => $email
              ); */
            $users[$email] = $uid;
        }
        fclose($fdi);


        $tables = array();

        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        $userids = array();
        $fdos = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            $fdos[$i] = fopen("cg_group_user_" . $i . ".sql", "wb");
        }
        foreach ($tables as $table) {
            if (strpos($table, "user_") === false) {
                continue;
            }
            $sql = "SELECT * FROM " . $table . "\n";
            $ret = $conn->getData($sql);
            foreach ($ret as $item) {

                //转换
                $uid = $users[$item['userid']];

                $ctime = $item['createtime'];
                $reqgroups = $item['reqgroups'];
                if ($reqgroups) {
                    $reqgroups = explode(",", $reqgroups);
                    foreach ($reqgroups as $groupid) {
                        $relation = DGroups_Const::GROUP_RELATION_MEMBER;

                        $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                        $arr = array(
                            "uid" => $uid,
                            "relation" => $relation,
                            "gid" => $groupid,
                            "ctime" => $ctime
                        );
                        if (!$uid || !$groupid) {
                            // print_r($item);
                            continue;
                        }
                        fwrite($fdos[$index], self::insert($arr, "cg_group_user_" . $index) . ";\n");
                    }
                }
                $groups = $item['groups'];
                if ($groups) {
                    $groups = get_object_vars(json_decode($groups));
                    foreach ($groups as $groupid => $usertype) {
                        if ($usertype == 1) {
                            $relation = DGroups_Const::GROUP_RELATION_MEMBER;
                        } else {
                            $relation = DGroups_Const::GROUP_RELATION_ADMIN;
                        }
                        $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                        $arr = array(
                            "uid" => $uid,
                            "relation" => $relation,
                            "gid" => $groupid,
                            "ctime" => $ctime
                        );
                        if (!$uid || !$groupid) {
                            //   print_r($item);
                            continue;
                        }
                        fwrite($fdos[$index], self::insert($arr, "cg_group_user_" . $index) . ";\n");
                    }
                }
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            fclose($fdos[$i]);
        }
    }

    public function dumpMemberInfoByMember($conn) {
        $ret = $conn->getData("show tables");
        $users = array();
        $fdi = fopen("cg_group_username.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($typeid, $uid, $username, $email) = explode("\t", $line);
            $users[$email] = $uid;
        }
        fclose($fdi);


        $tables = array();

        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        $userids = array();
        $fdos = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            $fdos[$i] = fopen("cg_group_user_member_" . $i . ".sql", "wb");
        }

        $rolemap = array(
            1 => DGroups_Const::GROUP_RELATION_MEMBER,
            2 => DGroups_Const::GROUP_RELATION_ADMIN,
            3 => DGroups_Const::GROUP_RELATION_CREATOR,
        );
        foreach ($tables as $table) {
            $sql = "SELECT * FROM " . $table . "\n";
            list($prefix, $groupid) = explode("_", $table);
            if ($prefix != "member") {
                continue;
            }
            $ret = $conn->getData($sql);
            foreach ($ret as $item) {
                // print_r($item);
                //转换
                $uid = $users[$item['userid']];
                $ctime = $item['createtime'];
                $role = $item['role'];
                $relation = $rolemap[$role];

                $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                $arr = array(
                    "uid" => $uid,
                    "relation" => $relation,
                    "gid" => $groupid,
                    "ctime" => $ctime
                );
                if (!$uid || !$groupid) {
                    // print_r($item);
                    continue;
                }
                fwrite($fdos[$index], self::insert($arr, "cg_group_user_" . $index) . ";\n");
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            fclose($fdos[$i]);
        }
    }

    public function dumpNotice($conn) {

        $users = array();

        $tables = array();
        $ret = $conn->getData("show tables");
        //  print_r($ret);
        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }


        $fdo = fopen("group_notice.sql", "wb");
        //   print_r($tables);
        foreach ($tables as $table) {
            $sql = "SELECT * FROM " . $table . "\n";
            $parts = explode("_", $table);
            if (count($parts) != 2) {
                continue;
            }
            list($prefix, $groupid) = $parts;
            if ($prefix != "bulletin") {
                continue;
            }
            //  echo $sql;
            $ret = $conn->getData($sql);
            foreach ($ret as $item) {
                $isdeleted = $item['isdeleted'];
                if ($isdeleted) {
                    continue;
                }

                $uparr = array
                    (
                    'notice' => $item['content'],
                    'atime' => $item['createtime']
                );
                $sql = DDb_Sql::update($uparr, "cg_group_info", " WHERE gid='" . intval($groupid) . "'");

                fwrite($fdo, $sql . ";\n");
            }
        }
        unset($tables);
        fclose($fdo);
    }

    public function dumpThread($conn) {

        $users = array();
        $fdi = fopen("cg_group_username.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($typeid, $uid, $username, $email) = explode("\t", $line);
            $users[$email] = $uid;
        }
        fclose($fdi);

        $tidmap = array();
        $fdi = fopen("group_tid_map.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($tid, $newtid) = explode("\t", $line);
            $tidmap[$tid] = $newtid;
        }
        fclose($fdi);

        $tables = array();
        $ret = $conn->getData("show tables");
        //  print_r($ret);
        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        $userids = array();
        $fdos = array();
        $fdots = array();
        $fdors = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            $fdos[$i] = fopen("cg_group_topic_title_" . $i . ".sql", "wb");
            $fdots[$i] = fopen("cg_group_topic_top_" . $i . ".sql", "wb");
            $fdors[$i] = fopen("cg_group_topic_rec_" . $i . ".sql", "wb");
        }
        $floormap = array();

        $fdo = fopen("group_thread_relation.txt", "wb");
        //   print_r($tables);
        foreach ($tables as $table) {
            $sql = "SELECT * FROM " . $table . "\n";
            $parts = explode("_", $table);
            if (count($parts) != 2) {
                continue;
            }
            list($prefix, $groupid) = $parts;
            if ($prefix != "art") {
                continue;
            }
            //  echo $sql;
            $ret = $conn->getData($sql);
            foreach ($ret as $item) {
                $isdeleted = $item['isdeleted'];
                if ($isdeleted) {
                    continue;
                }
                //print_r($item);
                //转换
                $uid = $users[$item['cn']];
                if (!$uid || !$groupid) {
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

                $istop = $item['istop'];
                $iselite = $item['iselite'];
                $islock = $item['islock'];

                $adminflag = 0;
                if ($istop) {
                    $adminflag = DUtil_Binary::set($adminflag, DGroups_Const::GROUP_TOPIC_ADMIN_FLAG_TOP, 1);
                    fwrite($fdots[$index], self::insert($arr, "cg_group_topic_top_" . $index) . ";\n");
                }

                if ($iselite) {
                    $adminflag = DUtil_Binary::set($adminflag, DGroups_Const::GROUP_TOPIC_ADMIN_FLAG_REC, 1);
                    fwrite($fdors[$index], self::insert($arr, "cg_group_topic_rec_" . $index) . ";\n");
                }

                if ($islock) {
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
                if ($thread_tid != $artid) {
                    $floor = $floormap[$thread_tid]++;
                }
                //回帖不入标题库
                if ($thread_tid == $artid) {

                    fwrite($fdos[$index], self::insert($arr, "cg_group_topic_title_" . $index) . ";\n");
                }
                if ($thread_tid == $artid) {
                    $floor = 0;
                    $thread_tid = 0;
                }
                fwrite($fdo, $artid . "\t" . $uid . "\t" . $isdeleted . "\t" . $floor . "\t" . $ctime . "\t" . $groupid . "\t" . $thread_tid . "\n");
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            fclose($fdos[$i]);
            fclose($fdots[$i]);
            fclose($fdors[$i]);
        }
    }

    public function buildTidMap($conn) {
        $ret = $conn->getData("show tables");

        $tables = array();

        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }

        $fdo = fopen("group_tid_map.txt", "wb");
        $ret = array();
        foreach ($tables as $table) {
            $sql = "SELECT  artid FROM " . $table . "\n";
            //echo $sql;
            $parts = explode("_", $table);
            if (count($parts) != 2) {
                continue;
            }
            list($prefix, $groupid) = $parts;
            if ($prefix != "art") {
                continue;
            }
            $tmp = $conn->getData($sql);
            foreach ($tmp as &$item) {
                $item['gid'] = $groupid;
            }
            $ret = array_merge($ret, $tmp);
        }
        unset($tables);

        foreach ($ret as $item) {
            $new_tid = DDb_IDGen::getNewId(DDb_Const::GROUP_TOPIC_TID_KEY);
            $artid = $item['gid'] . "_" . $item['artid'];
            fwrite($fdo, $artid . "\t" . $new_tid . "\n");
        }
        fclose($fdo);

        DDb_DBConfig::closeInstance();
    }

    public function dumpThreadReply($conn) {
        $ret = $conn->getData("show tables");
        $tidmap = array();
        $fdi = fopen("group_tid_map.txt", "rb");
        while ($line = fgets($fdi)) {
            $line = trim($line);
            list($tid, $newtid) = explode("\t", $line);
            $tidmap[$tid] = $newtid;
        }
        fclose($fdi);

        $artmap = array();
        $fdi = fopen("group_thread_relation.txt", "rb");
        while ($line = fgets($fdi)) {
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

        foreach ($ret as $item) {
            $tables[] = $item['Tables_in_group_audit'];
        }
        $userids = array();
        $fdos = array();
        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            $fdos[$i] = fopen("cg_group_topic_" . $i . ".sql", "wb");
        }

        foreach ($tables as $table) {
            $sql = "SELECT * FROM " . $table . "\n";
            $parts = explode("_", $table);
            if (count($parts) != 3) {
                continue;
            }
            list($prefix, $prefix2, $groupid) = $parts;
            if ($prefix != "art") {
                continue;
            }

            $ret = $conn->getData($sql);

            foreach ($ret as $item) {
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
                if (isset($artmap[$tid])) {
                    //print_r($artmap[$tid]);
                    $item = $artmap[$tid];
                    $thread_tid = $item["thread_tid"];
                    $uid = $item["uid"];
                    $isdeleted = $item['isdeleted'];
                    $floor = $item['floor'];
                    if ($isdeleted) {
                        $flag = DGroups_Const::GROUP_AUDIT_STATUS_DEL;
                    }
                    $ctime = $item['ctime'];
                } else {
                    //    echo "not set ".$tid." "."! \n";
                }
                //     error_reporting(E_ALL);
                //    ini_set("display_errors", 1);
                if ($tid == $thread_tid) {
                    $thread_tid = 0;
                }
                $index = $groupid % DDb_Const::SPLIT_TABLE_NUM;
                $arr = array(
                    "uid" => $uid,
                    "tid" => $tid,
                    "thread_tid" => $thread_tid,
                    "content" => DCntv_UBBHelper::decode($content, $groupid, $item['artid']), //DUtil_Str::ubb2html($content),
                    "gid" => $groupid,
                    "ctime" => $ctime,
                    "mtime" => $ctime,
                    "flag" => $flag,
                    "floor" => $floor
                );
                // print_r($arr);
                if (!$uid || !$groupid) {
                    // print_r($item);
                    continue;
                }
                fwrite($fdos[$index], self::insert($arr, "cg_group_topic_" . $index) . ";\n");
            }
        }
        unset($tables);

        for ($i = 0; $i < DDb_Const::SPLIT_TABLE_NUM; $i++) {
            fclose($fdos[$i]);
        }
    }

    public function main() {
        global $CONFS;
        $config = $CONFS['db'];
        $host = $config['host']['m'];
        $user = $config['user'];
        $passwd = $config['pass'];
        $port = $config['port'];
        $dbs = array(
            "group_audit",
            "group_audit",
            "group_audit"
        );

        switch ($this->type) {
            case "group":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[0]);
                $this->dumpGroupInfo($conn);
                break;
            case "user":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[1]);
                echo "开始导出用户信息\n";
                $this->dumpUserInfo($conn);
                echo "开始从用户表导出成员信息\n";
                $this->dumpMemberInfoByUser($conn);
                /* $ret = $conn->getData("show tables");
                  print_r($ret); */
                break;
            //print_r($ret);
            case "idmap":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[2]);
                $this->buildTidMap($conn);
                break;
            case "thread":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[2]);
                echo "开始导出群组帖子标题信息\n";
                $this->dumpThread($conn);
                echo "开始导出群组帖子内容信息\n";
                $this->dumpThreadReply($conn);
                //print_r($ret);
//print_r($CONFS['db']); 
                break;
            case "member":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[2]);
                $this->dumpMemberInfoByMember($conn);
                //      print_r($ret);
//print_r($CONFS['db']); 
                break;
            case "notice":
                $conn = new DDb_Handle($host, $port, $user, $passwd, $dbs[2]);
                $this->dumpNotice($conn);
                break;
        }
    }

}

$migrate = new migrate();
$migrate->run();
?>
