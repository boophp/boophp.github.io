<?php

include_once("../../conf/global.php");

/**
 * 导出数据库配置
 *
 * @package
 * @subpackage
 */
class dump extends DCore_BackApp
{

    private function outputArray($key, $item, &$content, $inner = false)
    {
        if ($inner)
        {
            $content .= "\t";
        }
        if (is_numeric($key))
        {
            $content .= "\t" . $key . "=>array(\n";
        }
        else
        {
            $content .= "\t\"" . $key . "\"=>array(\n";
        }

        foreach ($item as $subkey => $subitem)
        {
            if (is_array($subitem))
            {
                //print_r($subitem);
                $this->outputArray($subkey, $subitem, $content, true);
            }
            else
            {
                if ($inner)
                {
                    $content .= "\t\t";
                }
                if (is_numeric($subitem))
                {
                    $content.="\t\t\"" . $subkey . "\"=>" . $subitem . ", \n";
                }
                else
                {
                    $content.="\t\t\"" . $subkey . "\"=>\"" . $subitem . "\", \n";
                }
            }
        }
        if ($inner)
        {
            $content .= "\t";
        }
        $content .="\t) ,\n";

        return $content;
    }

    protected function outputDBConfig()
    {

        $config = DUtil_Base::getInstance("DDb_Config");

        $servers = $config->getServers();

        $masters = array();
        foreach ($servers as $server)
        {
            //主库，不运算
            if (!$server['master_sid'])
            {
                $masters[$server['sid']] = $server;
                continue;
            }
            $masters[$server['master_sid']]["slaves"][] = $server;
        }
        $content =
                '/**
  * 服务器映射表	
  */
global $server_map;
$server_map = array(
';
        foreach ($masters as $sid => $master)
        {
            $this->outputArray($sid, $master, $content);
        }
        $content .= '
);
';
        $content .=
                '/**
  * 表前缀配置映射表
  */
global $kind_map;
$kind_map = array(
';
        $kinds = $config->getKinds();
        $kindconfs = array();
        foreach ($kinds as $kind)
        {

            $kindconfs[$kind['kind']] = $kind;
            $this->outputArray($kind['kind'], $kind, $content);
        }

        $content .= '
);
';

        $tables = $config->getTables();
        $tableconfs = array();
        foreach ($tables as $table)
        {
            $no = $table['no'];
            $kind = $table['kind'];
            $tableconfs[$kind][$no]["sid"] = $table['sid'];
            $tableconfs[$kind][$no]["no"] = $table['no'];
            $tableconfs[$kind][$no]["db_name"] = $table['db_name'];
        }
        $content .=
                '/**
  * 表配置映射表
  */
global $table_map;
$table_map = array(
';
        foreach ($tableconfs as $kind => $table)
        {
            $subcontent = "";
            $this->outputArray($kind, $table, $subcontent);
            $content .=$subcontent;
        }
        $content .= '
);
';
        $dbconf = ROOT_DIR . DS . "conf" . DS . "db" . DS . "dbconf.php";
        $samplefile = $dbconf . ".sample";

        $content = file_get_contents($samplefile) . "\n" . $content;
        file_put_contents($dbconf, $content);
        //echo $content;
    }

    private function getDefaultConfValue($ptype)
    {
        $productname = strtoupper(DGroups_Const::$product_names[$ptype]);
        $content = "";
        switch ($ptype)
        {
            case DGroups_Const::GROUP_PRODUCT_CLUB:
                $content = ' 
/**
  * 单个用户发一条话题的限制是多少秒
  */
define("GLOBAL_CONF_' . $productname . '_TOPIC_SECONDS", 30);

/**
  * 单个用户一天发多少话题出验证码
  */
define("GLOBAL_CONF_' . $productname . '_CAPTCHA_TOPICS", 6); 
/**
  * 群话题标题最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MIN", 4); 

/**
  * 群话题标题最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MAX", 256);

/**
  * 群内容最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MIN", 10); 

/**
  * 群内容最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MAX", 40000);
/**
  * 评审类型，全局，先发后审，还是先审后发
  */
define("GLOBAL_CONF_' . $productname . '_AUDIT_TYPE", 0);
/**
  * 是否限制视频 URL 来源
  */
define("GLOBAL_CONF_' . $productname . '_LIMIT_URL", 1);
';
                break;
            case DGroups_Const::GROUP_PRODUCT_COMMON:
                $content = '
/**
  * 单个用户最多能创建多少普通群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_COMMON", 1);

/**
  * 单个用户最多能创建多少验证群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_INVITE"  , 1);

/**
  * 单个用户最多能创建多少隐私群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_PRIVATE", 0);

/**
  * 单个用户发一条话题的限制是多少秒
  */
define("GLOBAL_CONF_' . $productname . '_TOPIC_SECONDS", 30);

/**
  * 单个用户一天发多少话题出验证码
  */
define("GLOBAL_CONF_' . $productname . '_CAPTCHA_TOPICS", 6);

/**
  * 私密群成员上限
  */
define("GLOBAL_CONF_' . $productname . '_PRIVATE_MEMBER_NUM", 1000);

/**
  * 公共群成员上限
  */
define("GLOBAL_CONF_' . $productname . '_PUBLIC_MEMBER_NUM", 100000);

/**
  * 管理员数
  */
define("GLOBAL_CONF_' . $productname . '_ADMIN_NUM", 5);

/**
  * 群公告文字长度
  */
define("GLOBAL_CONF_' . $productname . '_NOTICE_LEN", 1000);
/**
  * 群简介文字长度
  */
define("GLOBAL_CONF_' . $productname . '_INTRO_LEN", 200);
/**
  * 群名字最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_NAME_MIN", 4); 

/**
  * 群名字最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_NAME_MAX", 40);
/**
  * 群话题标题最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MIN", 4); 

/**
  * 群话题标题最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MAX", 256);

/**
  * 群内容最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MIN", 10); 

/**
  * 群内容最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MAX", 40000);
/**
  * 评审类型，全局，先发后审，还是先审后发
  */
define("GLOBAL_CONF_' . $productname . '_AUDIT_TYPE", 0);

/**
  * 是否限制视频 URL 来源
  */
define("GLOBAL_CONF_' . $productname . '_LIMIT_URL", 1);

';
                break;
            case DGroups_Const::GROUP_PRODUCT_WEIBO:
                $content = '
/**
  * 单个用户最多能创建多少普通群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_COMMON", 1);

/**
  * 单个用户最多能创建多少验证群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_INVITE"  , 1);

/**
  * 单个用户最多能创建多少隐私群
  */
define("GLOBAL_CONF_' . $productname . '_MAX_PRIVATE", 0);

/**
  * 单个用户发一条话题的限制是多少秒
  */
define("GLOBAL_CONF_' . $productname . '_TOPIC_SECONDS", 30);

/**
  * 单个用户一天发多少话题出验证码
  */
define("GLOBAL_CONF_' . $productname . '_CAPTCHA_TOPICS", 6);

/**
  * 私密群成员上限
  */
define("GLOBAL_CONF_' . $productname . '_PRIVATE_MEMBER_NUM", 1000);

/**
  * 公共群成员上限
  */
define("GLOBAL_CONF_' . $productname . '_PUBLIC_MEMBER_NUM", 100000);

/**
  * 管理员数
  */
define("GLOBAL_CONF_' . $productname . '_ADMIN_NUM", 5);

/**
  * 群公告文字长度
  */
define("GLOBAL_CONF_' . $productname . '_NOTICE_LEN", 1000);
/**
  * 群简介文字长度
  */
define("GLOBAL_CONF_' . $productname . '_INTRO_LEN", 200);
/**
  * 群名字最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_NAME_MIN", 4); 

/**
  * 群名字最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_NAME_MAX", 40);
/**
  * 群话题标题最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MIN", 4); 

/**
  * 群话题标题最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_TITLE_MAX", 256);

/**
  * 群内容最短多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MIN", 10); 

/**
  * 群内容最长多少字符
  */
define("GLOBAL_CONF_' . $productname . '_CONTENT_MAX", 40000);
/**
  * 评审类型，全局，先发后审，还是先审后发
  */
define("GLOBAL_CONF_' . $productname . '_AUDIT_TYPE", 0);

/**
  * 是否限制视频 URL 来源
  */
define("GLOBAL_CONF_' . $productname . '_LIMIT_URL", 1);

';
                break;
        }
        return $content;
    }

    protected function outputLimit()
    {
        $limitconf = ROOT_DIR . DS . "conf" . DS . "limit" . DS . "limitconf.php";
        $samplefile = $limitconf . ".sample";

        $content = $this->getDefaultConfValue(DGroups_Const::GROUP_PRODUCT_COMMON);
        $content = str_replace("_" . strtoupper(DGroups_Const::$product_names[DGroups_Const::GROUP_PRODUCT_COMMON]) . "", "", $content);

        foreach (DGroups_Const::$product_names as $ptype => $item)
        {
            $api_admin = new DApi_Admin($ptype);
            $ret = $api_admin->getGroupConfList();

            if (empty($ret))
            {
                $tmp = $this->getDefaultConfValue($ptype);
                // echo $tmp."\n";
                $content = $content . "\n" . $tmp;
            }
            else
            {
                //保存一份最公共的
                $productname = strtoupper(DGroups_Const::$product_names[$ptype]);
                foreach ($ret as $item)
                {
                    if (!$item['cmemo'] || !$item['citem'] || $item['cvalue'] === '')
                    {
                        continue;
                    }
                    $itemstr = '/**
  * ' . $item['cmemo'] . '
  */
define("GLOBAL_CONF_' . $productname . "_" . strtoupper($item['citem']) . '",' . intval($item['cvalue']) . ');';
                    //  echo $itemstr."\n";
                    $content = $content . "\n" . $itemstr;
                }
            }
        }


        $content = file_get_contents($samplefile) . "\n" . $content;
        //  echo $content;

        file_put_contents($limitconf, $content);
    }

    protected function outputMemCache()
    {
        $memcacheconf = ROOT_DIR . DS . "conf" . DS . "cache" . DS . "memcacheconf.php";
        $samplefile = $memcacheconf . ".sample";

        $config = DUtil_Base::getInstance("DDb_Config");
        $servers = $config->getMemCacheServers();
        $content = '/**
  * 服务器映射表	
  */
global $memcache_map;
$memcache_map = array(
';
        foreach ($servers as $key => $item)
        {

            $this->outputArray($key, $item, $content);
        }
        $content .= ');';
        $content = file_get_contents($samplefile) . "\n" . $content;
        file_put_contents($memcacheconf, $content);
        //echo $content;
    }

    //必须先生成全局配置，再生成用户配置
    protected function outputUserLimit()
    {
        $limitconf = ROOT_DIR . DS . "conf" . DS . "limit" . DS . "limitconf.php";
        $fd = fopen($limitconf, "a+");

        foreach (DGroups_Const::$product_names as $ptype => $item)
        {
            $api_admin = new DApi_Admin($ptype);
            $result = $api_admin->getUserGroupConfList();
            $productname = strtoupper(DGroups_Const::$product_names[$ptype]);
            foreach ($result as $citem)
            {
                $value = $citem['cvalue'];
                $key = "USER_" . $citem['uid'] . "_" . $productname . "_" . strtoupper($citem['citem']) . "_CONF_VAL";
                $line = "define(\"" . $key . "\", " . $value . ")";
                fwrite($fd, $line . ";\n");
            }
        }
        fclose($fd);
    }

    protected function main()
    {
        $this->outputDBConfig();
        $this->outputMemCache();
        $this->outputLimit();
        $this->outputUserLimit();
    }

}

$dump = new dump();
$dump->run();
?>
