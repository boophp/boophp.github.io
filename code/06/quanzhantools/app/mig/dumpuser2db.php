<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class dump extends DCore_BackApp
{
    protected function main()
    {
        $filename = "alluser_info.txt";
        $fd = fopen($filename, "rb");
        
        $ofile = "cg_group_username.sql";
        $fdo = fopen($ofile, "wb");
        
        $infofile = "cg_group_username.txt";
        $fdinfo = fopen($infofile, "wb");
        

        while ($line = fgets($fd))
        {
            $line = trim($line);
            // echo $line."\n";
            $ret = json_decode($line);
            //print_r($ret);
            $ret = $ret->data;
            $usertype = $ret->userType1;
            $userId = $ret->userId;
            $nickName = $ret->nickname;
            $email = $ret->userName;
            
            if(!$userId || !nickName)
            {
                continue;
            }
            $arr = array(
                "uid" => $userId,
                "username" => $nickName,
                'user_type' => $usertype
            );
            $sql = DDb_Sql::insert($arr, "cg_group_username");
           
             $sql = str_replace("INSERT INTO", "INSERT IGNORE INTO", $sql);
          //    echo $sql."\n";
            fwrite($fdo,  $sql. ";\n");
 
           fwrite($fdinfo, $usertype."\t".$userId."\t".$nickName."\t".$email."\n");
        }
        //echo $filename . "\n";
        fclose($fd);
        fclose($fdinfo);
        fclose($fdo);
    }

}

$dump = new dump();
$dump->run();
?>