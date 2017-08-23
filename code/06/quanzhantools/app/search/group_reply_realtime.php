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
        $ptype = DGroups_Const::GROUP_PRODUCT_COMMON;
        $api_topic_handle = new DApi_Topic($ptype);
 
        $admin_db = new DAdmin_FuncDB($ptype);
        $topic_list = $admin_db->getTopReplyList(0, 10000, $total);
 
        $path = DCntv_Search::$basepath . "/" . DCntv_Search::$srcpath;
        $filename = $path . "/" . date("YmdHis") . "_topic.xml";
        //print_r($topic_list);
        echo " success count:" . count($topic_list) . "\n";
        $filename = DCntv_Search::genGroupTopicXmlFile($topic_list, DCntv_Search::INDEX_CMD_TYPE_NEW, DCntv_Search::INDEX_CONTENT_TYPE_GROUP_TOPIC, $filename);
        $post = array(
            "xmldata" => file_get_contents($filename)
        );
        
        //print_r($post);
        $ret = DUtil_Crawler::curl_crawl_page(DCntv_Search::INDEX_REALTIME_URL, "", true, $post);
        print_r($ret);
        exit;
    }

}

$group = new group();
$group->run();
?>
