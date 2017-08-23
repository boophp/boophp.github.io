<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class syncUser extends DCore_BackApp
{
    private $updateNickNameUrl = 'http://test3.audit.my.cntv.cn/audituser.php?a=getnewnickname&';
    
    protected function getParameter()
    {
        $this->startTime = $this->getParam("starttime");
        if (empty($this->startTime)) {
            $this->startTime = strtotime('-1 day');
        }
        $this->endTime = $this->getParam("endtime");
        if (empty($this->endTime)) {
            $this->endTime = time();
        }
    }
    
	public function main()
	{
        $this->updateNickNameUrl .= 'starttime='.$this->startTime.'&endtime='.$this->endTime;
		$result = DUtil_Crawler::curl_crawl_page($this->updateNickNameUrl);
        $result = json_decode($result, true);
        if (!$result['code']) {
            $userDb = new DGroups_GroupUsernameDB(DGroups_Const::GROUP_PRODUCT_COMMON);
            foreach($result['result'] as $uid => $nickname) {
                $userDb->updateGroupUsername($uid, $nickname);
            }
        }
		exit;
	}

}

$syncUser = new syncUser();
$syncUser->run();
