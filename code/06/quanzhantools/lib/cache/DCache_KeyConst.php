<?php
/**
 * 专门放置Memcache缓存的Key常量或前缀
 *
 * 统一方便地管理缓存
 *
 * @package 
 * @version $Id$
 * @copyright 2012 cyber4cn All Rights Reserved.
 * @author xuxinhua1984@gmail.com
 * @date 2012/05/27
 */
final class DCache_KeyConst
{
    const 
        // 缓存某个群所有话题总数
		GROUP_TOPIC_TOTAL_KEY_PREFIX = "group_topic_total_",
        // 缓存某个群置顶话题总数
		GROUP_TOP_TOPIC_TOTAL_KEY_PREFIX = "group_top_topic_total_",
        // 缓存某个群所有话题的所有回复总数
		GROUP_ALL_REPLY_KEY_PREFIX = "group_topic_total_",
        
        // 缓存某个群所有成员数
        GROUP_ALL_MEMBER_TOTAL_KEY_PREFIX = "group_all_memeber_total_",
        // 缓存某个群普通成员数
        GROUP_NORMAL_MEMBER_TOTAL_KEY_PREFIX = "group_normal_memeber_total_",
        // 缓存某个群管理员数
        GROUP_ADMIN_MEMBER_TOTAL_KEY_PREFIX = "group_admin_memeber_total_",

        // 用户喜欢某个话题（缓存是否已经喜欢过）
        TOPIC_ADD_LIKE_KEY_PREFIX           = 'topic_add_like_',
        
        // 群组分类缓存key
        GROUP_CATEGORY_LIST_KEY = 'group_category_list',

        // 群组 喜欢话题用户总数
        GROUP_LIKE_TOPIC_USER_TOTAL_KEY_PREFIX = 'group_like_topic_user_',

        // 微群发送的评论总数
		WEIQUN_REPLY_SEND_TOTAL_KEY_PREFIX = "weiqun_reply_send_total_uid_",
        // 微群收到的评论总数
		WEIQUN_REPLY_RECEIVE_TOTAL_KEY_PREFIX = "weiqun_reply_receive_total_uid_",
        // 微群提到我的微博总数
		WEIQUN_TOPIC_AT_TOTAL_KEY_PREFIX = "weiqun_topic_at_total_uid_",
        // 微群提到我的回复总数
		WEIQUN_REPLY_AT_TOTAL_KEY_PREFIX = "weiqun_reply_at_total_uid_",



        VERSION                     = 1;
}