<?php
// club_username表和club_qun表数据不完整，修复
include_once("../../conf/global.php");

class clubusername extends DCore_BackApp
{
    public function main()
    {
        $userNameDb = new DGroups_GroupUsernameDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $clubInfo = new DGroups_InfoDB(DGroups_Const::GROUP_PRODUCT_CLUB);
        $groupList = $clubInfo->getGroupList(0, 1000);
        foreach($groupList as $oneGroup) {
            $gid = $oneGroup['gid'];
            $clubid = $oneGroup['cuid'];
            $ctime = $oneGroup['ctime'];
            try {
                $clubQun = new DGroups_ClubQunDB();
                $clubQun->addClubQun(compact($gid, $clubid, $ctime));
                $userInfo = DCntv_User::getUserInfoByApi($uid);
                if ($userInfo) {
                    $username = $userInfo->nickname;
                    $userNameDb->addGroupUsername($uid, $username);
                }
            } catch (Exception $e) {
            }
        }
    }

}

$sync = new clubusername();
$sync->run();