<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class group extends DCore_BackApp
{

	public function main()
	{
		$api_topic_handle = new DApi_Topic(DGroups_Const::GROUP_PRODUCT_COMMON);

		$gid = 11;
		$topic_list = $api_topic_handle->getTopicList($gid, 0, 100);
		echo " success count:" . count($topic_list) . "\n";
		$filename = DCntv_Search::genGroupTopicXmlFile($topic_list);
		$post = array(
			"xmldata" => file_get_contents($filename)
		);
		//print_r($post);
		$ret = DUtil_Crawler::curl_crawl_page(DCntv_Search::INDEX_BATCH_URL, "", true, $post);
		print_r($ret);
		exit;
	}

}

$group = new group();
$group->run();
?>
