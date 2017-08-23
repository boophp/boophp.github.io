<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class Limit extends DCore_BackApp
{
    const BAN_WORDS_URL = 'http://comment.cntv.cn/interface/getkeywordbylevel?level=2', // 禁词
          SENSITIVE_WORDS_URL = 'http://comment.cntv.cn/interface/getkeywordbylevel?level=1';   // 敏感词

	public function main()
	{
        $params = array(
            array(self::BAN_WORDS_URL, 'banwords.txt'),
            array(self::SENSITIVE_WORDS_URL, 'sensitivewords.txt'),
        );
        foreach($params as $param) {
		    $this->getLimitWords($param);
        }
        exit;
	}

    private function getLimitWords($param)
    {
        $result = DUtil_Crawler::curl_crawl_page($param[0]);
        $result = json_decode($result, true);
        if ($result['ret']) {
            $words = implode('|', $result['data']);
            file_put_contents(ROOT_DIR.'/conf/words/'.$param[1], $words);
        }
    }
}

$limit = new Limit();
$limit->run();
