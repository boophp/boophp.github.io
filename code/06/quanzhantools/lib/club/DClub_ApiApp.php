<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DClub_ApiApp extends DCore_ApiApp
{
    /**
     *  置顶帖子
     */
    const API_METHOD_TOPIC_SETTOP = "topic.settop";
    /**
     *  解除置顶
     */
    const API_METHOD_TOPIC_UNSETTOP = "topic.unsettop";
    /**
     *  加为精华
     */
    const API_METHOD_TOPIC_SETREC = "topic.setrec";
    /**
     *  解除加精
     */
    const API_METHOD_TOPIC_UNSETREC = "topic.unsetrec";
    /**
     *  设置公告
     */
    const API_METHOD_TOPIC_SETACT = "topic.setact";
    /**
     *  解除设置公告
     */
    const API_METHOD_TOPIC_UNSETACT = "topic.unsetact";
    /**
     *  删除单个回复或者话题
     */
    const API_METHOD_TOPIC_DELETE = "topic.delete";
    /**
     *  锁定帖子
     */
    const API_METHOD_TOPIC_SETLOCK = "topic.setlock";
    /**
     *  解除锁定
     */
    const API_METHOD_TOPIC_UNSETLOCK = "topic.unsetlock";
    /**
     *  取得讨论区某页帖子列表
     */
    const API_METHOD_TOPIC_GETCLUBTOPICS = "topic.getclubtopics";
    /**
     * 取得精华帖子列表
     */
    const API_METHOD_TOPIC_GETCLUBRECTOPICS = "topic.getclubrectopics";
    /**
     * 取得活动公告列表
     */
    const API_METHOD_TOPIC_GETACTTOPICS = "topic.getacttopics";
    /**
     *  搜索帖子
     */
    const API_METHOD_TOPIC_SEARCH = "topic.search";
    /**
     *  取得话题详细列表
     */
    const API_METHOD_TOPIC_GETTHREAD = "topic.getthread";
    /**
     *  取得我发起的话题列表
     */
    const API_METHOD_TOPIC_GETMYTOPICS = "topic.getmytopics";
    /**
     *  获取我回复的话题列表
     */
    const API_METHOD_TOPIC_GETMYREPLYTOPICS = "topic.getmyreplytopics";
    /**
     * 取得帖子的赞信息
     */
    const API_METHOD_TOPIC_GETUPS = "topic.getups";
    /**
     * 发布话题
     */
    const API_METHOD_TOPIC_POSTTOPIC = "topic.posttopic";
    /**
     * 发表回复
     */
    const API_METHOD_TOPIC_POSTREPLY = "topic.postreply";
    /**
     * 更新话题
     */
    const API_METHOD_TOPIC_UPDATETOPIC = "topic.updatetopic";
    /**
     * 喜欢话题
     */
    const API_METHOD_TOPIC_ADDLIKE = "topic.addlike";
    /**
     * 取得喜欢话题的用户列表
     */
    const API_METHOD_TOPIC_LIKETOPICUSERLIST = "topic.liketopicuserlist";
    /**
     * 用户是否喜欢某一话题（一段时间内）
     */
    const API_METHOD_TOPIC_USERLIKETOPIC = "topic.userliketopic";
    /**
     * 举报话题或回复
     */
    const API_METHOD_TOPIC_REPORTTOPIC = "topic.reporttopic";
    /**
     * 上传本地图片
     */
    const API_METHOD_TOPIC_UPLOADPIC = "topic.uploadpic";
    /**
     * 上一主题的发布时间 
     */
    const API_METHOD_LAST_TOPIC_TIME = "topic.lasttopictime";
    /**
     * 用户【今天】发表的主题数
     */
    const API_METHOD_TODAY_TOPIC_NUM = "topic.todaytopicnum";
    /**
     * 搜索视频
     */
    const API_METHOD_SEARCH_VIDEO = "topic.searchvideo";

    static $valid_topic_method = array(
        self::API_METHOD_TOPIC_SETTOP => 1,
        self::API_METHOD_TOPIC_UNSETTOP => 1,
        self::API_METHOD_TOPIC_SETREC => 1,
        self::API_METHOD_TOPIC_UNSETREC => 1,
        self::API_METHOD_TOPIC_SETACT => 1,
        self::API_METHOD_TOPIC_UNSETACT => 1,
        self::API_METHOD_TOPIC_SETLOCK => 1,
        self::API_METHOD_TOPIC_UNSETLOCK => 1,
        self::API_METHOD_TOPIC_DELETE => 1,
        self::API_METHOD_TOPIC_GETCLUBTOPICS => 1,
        self::API_METHOD_TOPIC_GETCLUBRECTOPICS => 1,
        self::API_METHOD_TOPIC_GETACTTOPICS => 1,
        self::API_METHOD_TOPIC_SEARCH => 1,
        self::API_METHOD_TOPIC_GETTHREAD => 1,
        self::API_METHOD_TOPIC_GETMYTOPICS => 1,
        self::API_METHOD_TOPIC_GETMYREPLYTOPICS => 1,
        self::API_METHOD_TOPIC_GETUPS => 1,
        self::API_METHOD_TOPIC_POSTTOPIC => 1,
        self::API_METHOD_TOPIC_POSTREPLY => 1,
        self::API_METHOD_TOPIC_UPDATETOPIC => 1,
        self::API_METHOD_TOPIC_ADDLIKE => 1,
        self::API_METHOD_TOPIC_LIKETOPICUSERLIST => 1,
        self::API_METHOD_TOPIC_USERLIKETOPIC => 1,
        self::API_METHOD_TOPIC_REPORTTOPIC => 1,
        self::API_METHOD_TOPIC_UPLOADPIC => 1,
        self::API_METHOD_LAST_TOPIC_TIME => 1,
        self::API_METHOD_TODAY_TOPIC_NUM => 1,
        self::API_METHOD_SEARCH_VIDEO => 1,
    );

    /**
     * 取得讨论区首页的广告
     */
    const API_METHOD_AD_GETHOME = "ad.gethome";
    /**
     * 取得话题最终页的广告
     */
    const API_METHOD_AD_GETTHREAD = "ad.getthread";

    static $valid_ad_method = array(
        self::API_METHOD_AD_GETHOME => 1,
        self::API_METHOD_AD_GETTHREAD => 1,
    );

    /**
     * 创建群
     */
    const API_METHOD_GROUP_CREATE = "group.create";
    
    /**
     * 获得群ID，根据userid 
     */
    const API_METHOD_GROUP_GETGID = "group.getgid";
    
    /**
     * 搜索群
     */
    const API_METHOD_GROUP_SEARCH = "group.search";

    /**
     *获得配置
     * @var type 
     */
    const API_METHOD_GROUP_GETCONF = "group.getconf";

    static $valid_group_method = array(
        self::API_METHOD_GROUP_CREATE => 1,
        self::API_METHOD_GROUP_GETGID => 1,
        self::API_METHOD_GROUP_SEARCH => 1,
        self::API_METHOD_GROUP_GETCONF =>1,
    );
    static $method_parameters = array(
        self::API_METHOD_GROUP_CREATE => array(
            "note" => "根据俱乐部账号来创建讨论区",
            "params" => array(
                "clubuserid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "俱乐部账号uid"
                ),
                "clubusername" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "俱乐部账号"
                )
        )),
        self::API_METHOD_GROUP_GETGID => array(
            "note" => "根据俱乐部账号UID获得讨论区ID",
            "params" => array(
                "clubuserid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "俱乐部账号uid"
                ),
        )),
        self::API_METHOD_GROUP_SEARCH => array(
            "note" => "根据关键字来搜索群名称(可能不需要)",
            "params" => array(
                "keyword" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "群名称关键字"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始条数"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示条数"
            ))
        ),
        self::API_METHOD_AD_GETHOME => array(
            "note" => "取得讨论区话题列表页的广告",
        ),
        self::API_METHOD_AD_GETTHREAD => array(
            "note" => "取得讨论区帖子区的广告",
        ),
        self::API_METHOD_TOPIC_GETCLUBTOPICS => array(
            "note" => "取得讨论区某页帖子列表",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始条数"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示标题条数"
            ))
        ),
        self::API_METHOD_TOPIC_GETACTTOPICS => array(
            "note" => "取得活动公告列表",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始条数"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示标题条数"
            ))
        ),
        self::API_METHOD_TOPIC_GETCLUBRECTOPICS => array(
            "note" => "获取群加精话题列表",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始条数"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示标题条数"
            ))
        ),
        self::API_METHOD_TOPIC_SETTOP => array(
            "note" => "对指定话题置顶",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_UNSETTOP => array(
            "note" => "取消指定话题的置顶",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_SETREC => array(
            "note" => "对指定话题加精",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_UNSETREC => array(
            "note" => "取消指定话题的加精",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_SETLOCK => array(
            "note" => "对指定话题锁定，不再让回复",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_UNSETLOCK => array(
            "note" => "取消指定话题的锁定",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_SETACT => array(
            "note" => "将指定话题设置为活动公告",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_UNSETACT => array(
            "note" => "取消对话题的活动公告的设置",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题ID"
            ))
        ),
        self::API_METHOD_TOPIC_DELETE => array(
            "note" => "删除指定话题或者回复",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "删除的用户UID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题或者回复ID"
            ))),
        self::API_METHOD_TOPIC_GETMYTOPICS => array(
            "note" => "取得我的话题列表",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID（当前用户）"
                ),
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示回复数"
                )
            )
        ),
        self::API_METHOD_TOPIC_GETMYREPLYTOPICS => array(
            "note" => "取得我回复的话题列表",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID（当前用户）"
                ),
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示回复数"
                )
            )
        ),
        self::API_METHOD_TOPIC_GETTHREAD => array(
            "note" => "取得话题的主帖和回复，用于最终话题详情页显示",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "话题或者回复ID"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示回复数"
                ),
                "usedetail" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "是否获取内容"
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID（当前用户，即查看该内容的用户）"
                ),
            )
        ),
        self::API_METHOD_TOPIC_SEARCH => array(
            "note" => "搜索话题接口",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID"
                ),
                "type" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "搜索类型 可以是 author/title/fulltext"
                ),
                "keyword" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "搜索关键字"
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示结果数"
                )
            )
        ),
        self::API_METHOD_TOPIC_POSTTOPIC => array(
            "note" => "发主帖",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "发帖用户UID",
                ),
                "nickname" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "发帖人昵称",
                ),
                "title" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "帖子标题",
                ),
                "content" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "帖子内容",
                ),
                "audittype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "审核类型  0 => 先审后发  1=>先发后审",
                ),
                "usertype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户类型 0 =>  个人用户  1=>俱乐部用户",
                ),
                "ip" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => " ip地址",
                ),
                "lock" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "是否禁止回复：0 => 允许回复；1 => 禁止回复",
                ),
            )
        ),
        self::API_METHOD_TOPIC_POSTREPLY => array(
            "note" => "发回复",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "发帖用户UID",
                ),
                "nickname" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "发帖人昵称",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "主帖编号",
                ),
                "content" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "帖子内容",
                ),
                "audittype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "审核类型  0 => 先审后发  1=>先发后审",
                ),
                "usertype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户类型 0 =>  个人用户  1=>俱乐部用户",
                ),
                "ip" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => " ip地址",
                ),
            )
        ),
        self::API_METHOD_TOPIC_UPDATETOPIC => array(
            "note" => "更新主帖",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "发帖用户UID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "要更新的帖子TID",
                ),
                "nickname" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "发帖人昵称",
                ),
                "title" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "帖子标题",
                ),
                "content" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "帖子内容",
                ),
                "audittype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "审核类型  0 => 先审后发  1=>先发后审",
                ),
                "usertype" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户类型 0 =>  个人用户  1=>俱乐部用户",
                ),
                "ip" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => " ip地址",
                ),
                "lock" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "是否禁止回复：0 => 允许回复；1 => 禁止回复",
                ),
            )
        ),
        self::API_METHOD_TOPIC_ADDLIKE => array(
            "note" => "赞话题",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "赞的用户ID",
                ),
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "被赞的话题TID",
                ),
            )
        ),
        self::API_METHOD_TOPIC_LIKETOPICUSERLIST => array(
            "note" => "获得赞过话题的用户列表",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "被赞的话题TID",
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示结果数"
                )
            )
        ),
        self::API_METHOD_TOPIC_USERLIKETOPIC => array(
            "note" => "判断用户一段时间内是否赞过某个主题",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID",
                ),
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "被赞的话题TID",
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示结果数"
                )
            )
        ),
        self::API_METHOD_TOPIC_REPORTTOPIC => array(
            "note" => "举报话题或回复",
            "params" => array(
                "gid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "讨论区ID",
                ),
                "tid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "被举报话题TID",
                ),
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "举报者用户UID",
                ),
                "reason" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "举报原因",
                ),
                "description" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "其他说明（备注）",
                ),
            )
        ),
        self::API_METHOD_GROUP_GETCONF => array(
             "note" => "取得配置",
            "params" => array(                
            )
        ),
        self::API_METHOD_LAST_TOPIC_TIME => array(
            "note" => "获得某用户上篇主题的发布时间",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID",
                ),
            )
        ),
        self::API_METHOD_TODAY_TOPIC_NUM => array(
            "note" => "获得用户【今天】发表的话题数",
            "params" => array(
                "uid" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "用户UID",
                ),
            )
        ),
        self::API_METHOD_SEARCH_VIDEO => array(
            "note" => "搜索视频",
            "params" => array(
                "searchKey" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "搜索关键字",
                ),
                "url" => array(
                    "type" => DCore_Input::TYPE_STR,
                    "note" => "视频url",
                ),
                "start" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "起始号"
                ),
                "num" => array(
                    "type" => DCore_Input::TYPE_UINT,
                    "note" => "每页显示结果数"
                )
            )
        ),
    );
    protected $method;
    protected $valid_method;

    protected $ptype;
    protected $sign;
    
    public function  __construct()
    {
        $this->ptype = DGroups_Const::GROUP_PRODUCT_CLUB;
         $this->valid_method = self::$valid_group_method;
       
    }

    const CHECK_AUTH_KEY = "iz17Rf21j06a6cRy2PfU08iVTDU9Uy8K3KOuhwaC4nC2C0Aq";
    
    protected function getPara()
    {
        $this->method = $this->getParam("method", "");
         $this->rsign = $this->getParam("sign");         
    }
    
    protected  function checkAuth()
    {       
        list($method, $time, $md5) =  explode("_", $this->rsign);
        if(md5($method.".".$time.".".self::CHECK_AUTH_KEY) != $md5)
        {
            $code = DExcept_Const::AUTH_EXCEPTION_CODE_SIGN_ERROR;
            throw new DExcept_AuthException($code);
        }
    }
    
    protected function checkPara()
    {
        if (!$this->method || !$this->rsign || !isset($this->valid_method[$this->method]))
        {
            $code = DExcept_Const::API_EXCEPTION_CODE_INVALID_METHOD;
            throw new DExcept_ApiException($code);
        }
    }

}

?>
