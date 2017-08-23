<?php
/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */
/**
 * 生成限制配置
 *
 * @package config
 * @author cyber4cn@gmail.com
 */
 

/**
  * 单个用户最多能创建多少普通群
  */
define("GLOBAL_CONF_MAX_COMMON", 1);

/**
  * 单个用户最多能创建多少验证群
  */
define("GLOBAL_CONF_MAX_INVITE"  , 1);

/**
  * 单个用户最多能创建多少隐私群
  */
define("GLOBAL_CONF_MAX_PRIVATE", 0);

/**
  * 单个用户发一条话题的限制是多少秒
  */
define("GLOBAL_CONF_TOPIC_SECONDS", 30);

/**
  * 单个用户一天发多少话题出验证码
  */
define("GLOBAL_CONF_CAPTCHA_TOPICS", 6);

/**
  * 私密群成员上限
  */
define("GLOBAL_CONF_PRIVATE_MEMBER_NUM", 1000);

/**
  * 公共群成员上限
  */
define("GLOBAL_CONF_PUBLIC_MEMBER_NUM", 100000);

/**
  * 管理员数
  */
define("GLOBAL_CONF_ADMIN_NUM", 5);

/**
  * 群公告文字长度
  */
define("GLOBAL_CONF_NOTICE_LEN", 1000);
/**
  * 群简介文字长度
  */
define("GLOBAL_CONF_INTRO_LEN", 200);
/**
  * 群名字最短多少字符
  */
define("GLOBAL_CONF_NAME_MIN", 4); 

/**
  * 群名字最长多少字符
  */
define("GLOBAL_CONF_NAME_MAX", 40);
/**
  * 群话题标题最短多少字符
  */
define("GLOBAL_CONF_TITLE_MIN", 4); 

/**
  * 群话题标题最长多少字符
  */
define("GLOBAL_CONF_TITLE_MAX", 256);

/**
  * 群内容最短多少字符
  */
define("GLOBAL_CONF_CONTENT_MIN", 10); 

/**
  * 群内容最长多少字符
  */
define("GLOBAL_CONF_CONTENT_MAX", 40000);
/**
  * 评审类型，全局，先发后审，还是先审后发
  */
define("GLOBAL_CONF_AUDIT_TYPE", 0);

/**
  * 是否限制视频 URL 来源
  */
define("GLOBAL_CONF_LIMIT_URL", 1);


/**
  * 一用户可创建最大公开群数
  */
define("GLOBAL_CONF_GROUP_MAX_COMMON",100);
/**
  * 一用户可创建最大验证群数
  */
define("GLOBAL_CONF_GROUP_MAX_INVITE",2);
/**
  * 一用户可创建最大私密群数
  */
define("GLOBAL_CONF_GROUP_MAX_PRIVATE",0);
/**
  * 发一篇帖子的时间间隔
  */
define("GLOBAL_CONF_GROUP_TOPIC_SECONDS",30);
/**
  * 一个用户一天发多少帖子开始出验证码
  */
define("GLOBAL_CONF_GROUP_CAPTCHA_TOPICS",3);
/**
  * 私密群成员上限
  */
define("GLOBAL_CONF_GROUP_PRIVATE_MEMBER_NUM",1000);
/**
  * 公共群成员上限
  */
define("GLOBAL_CONF_GROUP_PUBLIC_MEMBER_NUM",4);
/**
  * 管理员数目
  */
define("GLOBAL_CONF_GROUP_ADMIN_NUM",5);
/**
  * 群公告长度
  */
define("GLOBAL_CONF_GROUP_NOTICE_LEN",1000);
/**
  * 群简介长度
  */
define("GLOBAL_CONF_GROUP_INTRO_LEN",200);
/**
  * 群名称长度下限
  */
define("GLOBAL_CONF_GROUP_NAME_MIN",2);
/**
  * 群名称长度上限
  */
define("GLOBAL_CONF_GROUP_NAME_MAX",20);
/**
  * 群话题标题长度下限
  */
define("GLOBAL_CONF_GROUP_TITLE_MIN",2);
/**
  * 群话题标题长度上限
  */
define("GLOBAL_CONF_GROUP_TITLE_MAX",30);
/**
  * 群话题内容长度下限
  */
define("GLOBAL_CONF_GROUP_CONTENT_MIN",10);
/**
  * 群话题内容长度上限
  */
define("GLOBAL_CONF_GROUP_CONTENT_MAX",40000);
/**
  * 是否限制发照片视频的来源网络链接
  */
define("GLOBAL_CONF_GROUP_LIMIT_URL",1);
/**
  * 表示多少天能赞一次；0表示只能赞一次
  */
define("GLOBAL_CONF_GROUP_LIKE_NUM",1);
/**
  * 发一篇帖子的时间间隔
  */
define("GLOBAL_CONF_CLUBQUN_TOPIC_SECONDS",30);
/**
  * 一个用户一天发多少帖子开始出验证码
  */
define("GLOBAL_CONF_CLUBQUN_CAPTCHA_TOPICS",3);
/**
  * 群话题标题长度下限
  */
define("GLOBAL_CONF_CLUBQUN_TITLE_MIN",4);
/**
  * 群话题标题长度上限
  */
define("GLOBAL_CONF_CLUBQUN_TITLE_MAX",256);
/**
  * 群话题内容长度下限
  */
define("GLOBAL_CONF_CLUBQUN_CONTENT_MIN",10);
/**
  * 群话题内容长度上限
  */
define("GLOBAL_CONF_CLUBQUN_CONTENT_MAX",40000);
/**
  * 是否限制发照片视频的来源网络链接
  */
define("GLOBAL_CONF_CLUBQUN_LIMIT_URL",1);
/**
  * 表示多少天能赞一次；0表示只能赞一次
  */
define("GLOBAL_CONF_CLUBQUN_LIKE_NUM",1);
/**
  * 显示前多少个赞的用户
  */
define("GLOBAL_CONF_CLUBQUN_SHOW_LIKE_USER_NUM",2);

/**
  * 单个用户最多能创建多少普通群
  */
define("GLOBAL_CONF_WEIQUN_MAX_COMMON", 1);

/**
  * 单个用户最多能创建多少验证群
  */
define("GLOBAL_CONF_WEIQUN_MAX_INVITE"  , 1);

/**
  * 单个用户最多能创建多少隐私群
  */
define("GLOBAL_CONF_WEIQUN_MAX_PRIVATE", 0);

/**
  * 单个用户发一条话题的限制是多少秒
  */
define("GLOBAL_CONF_WEIQUN_TOPIC_SECONDS", 30);

/**
  * 单个用户一天发多少话题出验证码
  */
define("GLOBAL_CONF_WEIQUN_CAPTCHA_TOPICS", 6);

/**
  * 私密群成员上限
  */
define("GLOBAL_CONF_WEIQUN_PRIVATE_MEMBER_NUM", 1000);

/**
  * 公共群成员上限
  */
define("GLOBAL_CONF_WEIQUN_PUBLIC_MEMBER_NUM", 100000);

/**
  * 管理员数
  */
define("GLOBAL_CONF_WEIQUN_ADMIN_NUM", 5);

/**
  * 群公告文字长度
  */
define("GLOBAL_CONF_WEIQUN_NOTICE_LEN", 1000);
/**
  * 群简介文字长度
  */
define("GLOBAL_CONF_WEIQUN_INTRO_LEN", 200);
/**
  * 群名字最短多少字符
  */
define("GLOBAL_CONF_WEIQUN_NAME_MIN", 4); 

/**
  * 群名字最长多少字符
  */
define("GLOBAL_CONF_WEIQUN_NAME_MAX", 40);
/**
  * 群话题标题最短多少字符
  */
define("GLOBAL_CONF_WEIQUN_TITLE_MIN", 4); 

/**
  * 群话题标题最长多少字符
  */
define("GLOBAL_CONF_WEIQUN_TITLE_MAX", 256);

/**
  * 群内容最短多少字符
  */
define("GLOBAL_CONF_WEIQUN_CONTENT_MIN", 10); 

/**
  * 群内容最长多少字符
  */
define("GLOBAL_CONF_WEIQUN_CONTENT_MAX", 40000);
/**
  * 评审类型，全局，先发后审，还是先审后发
  */
define("GLOBAL_CONF_WEIQUN_AUDIT_TYPE", 0);

/**
  * 是否限制视频 URL 来源
  */
define("GLOBAL_CONF_WEIQUN_LIMIT_URL", 1);

