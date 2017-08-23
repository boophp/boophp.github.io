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

        $infofile = "alluser_info.txt";
        if (is_file($infofile) && filesize($infofile))
        {
            return;
        }
        $emails = array();
        $fd = fopen("now_member_emails.txt", "rb");
        while ($line = fgets($fd))
        {
            $emails[trim($line)] = 1;
        }
        fclose($fd);
       // print_r($emails);

        $userfile = "alluser.txt";
        $fdo = fopen($userfile, "wb");

        $fd = fopen(ROOT_DIR . "/doc/20120610/user2.txt", "rb");
        while ($line = fgets($fd))
        {
            $line = trim($line);
            list($uid, $email) = explode(",", trim($line));
        //    echo $email."\n";
            if (isset($emails[$email]))
            {
                //$userinfo = DCntv_User::getUserInfoJsonByApi($uid);
                //  echo ($userinfo)."\n";
                fwrite($fdo, $line . "\n");
            }
        }
        fclose($fd);
        $fd = fopen(ROOT_DIR . "/doc/20120610/user2.txt", "rb");
        while ($line = fgets($fd))
        {
            $line = trim($line);
            list($uid, $email) = explode(",", trim($line));
            if (isset($emails[$email]))
            {
                //$userinfo = DCntv_User::getUserInfoJsonByApi($uid);
                // echo ($userinfo)."\n";
                fwrite($fdo, $line . "\n");
            }
        }
        fclose($fd);
        fclose($fdo);

        $fd = fopen($userfile, "rb");
        $fdo = fopen($infofile, "wb");

        while ($line = fgets($fd))
        {
            $line = trim($line);
            list($uid, $email) = explode(",", trim($line));

            $userinfo = DCntv_User::getUserInfoJsonByApi($uid);
            echo ($userinfo) . "\n";
            fwrite($fdo, $userinfo . "\n");
        }
        fclose($fdo);
        fclose($fd);
    }

}

$dump = new dump();
$dump->run();
?> 
