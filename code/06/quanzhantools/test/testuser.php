
<?php

/**
 *
 *
 * @package
 * @subpackage
 */
require_once('../../simpletest/autorun.php');
require_once('../conf/global.php');

class TestTopic extends UnitTestCase
{

    public function testAddUser()
    {
        $uid = DDb_IDGen::getNewId("uid");
        $arr['uid'] = $uid;
        $arr['username'] = "testuser" . $uid;
        $arr['realname'] = "realname" . $uid;
        $arr['email'] = "email$uid@email.com";
        $arr['salt'] = rand(1000, 9999);
        $arr['password'] = md5($arr['slat'] . "#password$uid");

        $ret = DDb_DB::insert("qz_user", $uid, $arr);

        $ret = DDb_DB::getTableRow("qz_user", $uid, "uid=$uid");
        print_r($ret);
        $this->assertTrue(count($ret) > 0);
    }

    public function testGetUsers()
    {
        for ($uid = 1; $uid < 10; $uid++)
        {
            $ret = DDb_DB::getTableRow("qz_user", $uid, "uid=$uid");
            $this->assertTrue(count($ret) > 0);
        }
    }

}
?>
