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
        $api_topic_handle = new DApi_Topic(DGroups_Const::GROUP_PRODUCT_CLUB);
        $api_info_handle = new DApi_Info(DGroups_Const::GROUP_PRODUCT_CLUB);

        $group_list = $api_info_handle->getGroupList(0, 2000);
        $path = DCntv_Search::$basepath . "/" . DCntv_Search::$srcpath;
        $filename = $path . "/clubqun_" . date("YmdHis") . "_topic.xml";

        foreach ($group_list as $groupitem)
        {
            $gid = $groupitem['gid'];
         
            $topic_list = $api_topic_handle->getTopicList($gid, 0, 1000);
            
            $tids = DDb_Util::item_arr2ids($topic_list, "tid");
            //print_r($tids);
            if(empty($tids))
            {
                continue;
            }
            
            $content_list = $api_topic_handle->getTopicListByTids($gid, $tids);
            
            $content_list = DUtil_Base::toKeyIndexed($content_list, "tid");
            foreach($topic_list as &$item)
            {
                $item = array_merge($item, $content_list[$item['tid']]);
            }
        //    print_r($topic_list);
            echo " success count:" . count($topic_list) . "\n";
            $filename = DCntv_Search::genGroupTopicXmlFile($topic_list, DCntv_Search::INDEX_CMD_TYPE_NEW, DCntv_Search::INDEX_CONTENT_TYPE_CLUBQUN_TOPIC, $filename);
            $post = array(
                "xmldata" => file_get_contents($filename)
            );
        }
        //exit;
        //print_r($post);
        $ret = DUtil_Crawler::curl_crawl_page(DCntv_Search::INDEX_REALTIME_URL, "", true, $post);
        print_r($ret);
        exit;
    }

}

$group = new group();
$group->run();
?>
