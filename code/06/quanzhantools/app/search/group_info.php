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
		$api_info_handle = new DApi_Info(DGroups_Const::GROUP_PRODUCT_COMMON);

		$group_list = $api_info_handle->getGroupList(0, 100);
		echo " success count:".count($group_list)."\n";
		$filename = DCntv_Search::genGroupInfoXmlFile($group_list);
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
