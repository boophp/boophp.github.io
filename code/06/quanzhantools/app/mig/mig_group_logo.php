<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class mig extends DCore_BackApp
{

    protected function main()
    {
        $ptype = DGroups_Const::GROUP_PRODUCT_COMMON;
        $info_api = new DApi_Info($ptype);

        $grouplist = $info_api->getGroupList(0, 1000);

        $member_api = new DApi_Member($ptype);
        //print_r($grouplist);
        $info_func = new DFunc_Info($ptype);
        //fix member
        foreach ($grouplist as $item)
        {
            $gid = $item['gid'];
            $membercount = $member_api->getMembersCount($gid);
            //  echo $gid."\t".$membercount."\n";
            $arr = array(
                "membercount" => $membercount,
            );

            $cuid = $item['cuid'];
            try
            {
                $member_api->addMembers($gid, $cuid, DGroups_Const::GROUP_RELATION_CREATOR, "");
            }
            catch (Exception $ex)
            {
                $member_api->updateMembers($gid, $cuid, DGroups_Const::GROUP_RELATION_CREATOR, "");
            }
            //$info_func->updateGroupInfoEx($gid, $arr, array());
        }
        $api_upload = new DApi_Upload("logo");
        //fix logo
        $rootdir = "/data/grouppic/logo/temp/";
        foreach ($grouplist as $item)
        {
            $gid = $item['gid'];
            $dir = $gid % 1000;

            $destdir = $rootdir . "$gid/201207/";

            if (!is_dir($destdir))
            {
                system("mkdir -p " . $destdir);
            }
            $filename = "";
            if (is_file($dir . DS . $gid . ".jpg"))
            {
                $filename = $gid . ".jpg";
                $srcfile = $dir . DS . $filename;
                $destfile = $destdir . "/" . $filename;
            }
            else if (is_file($dir . DS . $gid . ".png"))
            {
                $filename = $gid . ".png";
                $srcfile = $dir . DS . $filename;
                $destfile = $destdir . "/" . $filename;
            }
            else
            {
                continue;
            }
            if (!is_file(dirname($destfile)))
            {
                system("mkdir -p " . dirname($destfile));
                system("chmod 777 -R ".dirname($destfile));
            }
            // echo $destfile."\n";
            $cmd = "cp " . $srcfile . " " . $destfile;
            system($cmd);

            $ret = $api_upload->temp2audit_logo($gid, "201207" . DS . $filename);
           // $ret = json_decode($ret);
           print_r($ret);
           // echo "audit2ol\n";
            $fn = basename($ret->content->distFn);

            $ret = $api_upload->audit2ol_logo($gid, $fn);

            $arr = array(
                "logo" => $ret->content->distFn,
            );
            $info_func->updateGroupInfo($gid, $arr, array());



            //   echo "$gid\n";
            //exit;
            //$ret = $api_upload->audit2ol_logo($gid, "201206");
        }
    }

}

$mig = new mig();
$mig->run();
?>
