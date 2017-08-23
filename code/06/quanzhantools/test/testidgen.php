<?php
//1-20000=》xxxx
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
	public function testGetNewTopicid()
	{
		$gid = DDb_IDGen::getNewId("gid");
		$this->assertTrue($gid>0);
	}
}
?>
