<?php

/**
 * 微博解析类
 * @package common
 * @author
 */
final class DParse_WeiboParse
{

    /**
     * 解析一条微博信息
     * 
     * @param string $text
     * @return array 
     *      array(
     *          [topic]	= array(),
     *          [at]    = array(),
     *          [link]	= array(),
     *          [sLink]	= array(),
     *          [sText] = string,
     *          [text]  = string,
     *      )
     */
    public static function parseWbText($text, $showShorUrl = false)
    {
        $arrResult = array(
            'topic' => array(), // 话题
            'at' => array(), // @
            'link' => array(), // 链接
            'sLink' => array(), // 短链接
            'sText' => $text, // 原始文本
            'text' => '', // 转换后
        );

        if (empty($text))
        {
            return $arrResult;
        }

        $arrPattern = array(
            '#[^#]+#', //话题
            '[a-z0-9\-_]*[a-z0-9]@(?:[a-z0-9-]+)(?:\.[a-z0-9-]+)+', //邮件地址 为了区分 @xxxx
            '@[\x{4e00}-\x{9fa5}0-9A-Za-z_\-]+', //@XXXXX
            'https?://[\w-\.]+[\w-]+(?:/[\w-_~!@#\$%\^&\*()\+-=\{\}\|\\\\:"\;\'<>\?,\./]*)?'//链接
        );

        $pattern = ';(' . implode('|', $arrPattern) . ');sium';
        $arrMatches = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $arrNode = array();

        foreach ($arrMatches as $i => $v)
        {

            // 奇数节点都是正则匹配的部分（即满足$arrPattern）
            if ($i % 2)
            {

                if (substr($v, 0, 1) . substr($v, -1, 1) == '##')
                {
                    //话题
                    $arrResult['topic'][] = substr($v, 1, -1);
                }
                elseif (substr($v, 0, 1) == '@')
                {
                    //@某人
                    $arrResult['at'][] = substr($v, 1);
                }
                elseif (preg_match("#^http://(?:sinaurl|t|cntvurl|u\.cntv)\.cn/[a-z0-9]+\$#sim", $v))
                {
                    //短链接
                    $arrResult['sLink'][] = $v;
                    $v = preg_replace("#http://(?:sinaurl|t|cntvurl)\.cn/#si", OAPI_CNTV_SURL, $v);
                }
                elseif (preg_match("#^https?://.+\$#sim", $v))
                {

                    //普通链接，转成短链接
                    $lUrl = explode('/', strtolower($v));
                    $noUrl = array(
                        't.sina.com.cn',
                        'weibo.com'
                    );
                    if (!in_array($lUrl[2], $noUrl))
                    {
                        $arrResult['link'][] = $v;
                    }
                }
            }
            $arrNode[] = $v;
        }

        if (!empty($arrResult['link']) && $showShorUrl)
        {
            try
            {
                $sUrl = self::shortUrl($arrResult['link']);
                $tmp = array();
                foreach ($sUrl as $v)
                {
                    $lk = strtolower($v['url_long']);
                    $tmp[$lk] = preg_replace("#http://(?:sinaurl|t)\.cn/#si", OAPI_CNTV_SURL, $v['url_short']);
                }
            }
            catch (Exception $ex)
            {
                
            }

            foreach ($arrNode as $node)
            {
                $lk = strtolower($node);
                $arrResult['text'] .= isset($tmp[$lk]) ? $tmp[$lk] : $node;
            }
        }
        else
        {
            $arrResult['text'] = implode('', $arrNode);
        }

        return $arrResult;
    }

    public static function parseLink2Short($text, &$linkmap)
    {
        if (empty($text))
        {
            return $text;
        }

        $links = array();   // 链接

        $arrPattern = array(
            'https?://[\w-\.]+[\w-]+(?:/[\w-_~!@#\$%\^&\*()\+-=\{\}\|\\\\:"\;\'<>\?,\./]*)?'//链接
        );

        $pattern = ';(' . implode('|', $arrPattern) . ');sium';
        $arrMatches = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $arrNode = array();

        foreach ($arrMatches as $i => $v)
        {
            // 奇数节点都是正则匹配的部分（即满足$arrPattern）
            if ($i % 2)
            {
                // 本身就是短链接
                if (preg_match("#^http://(?:sinaurl|t|cntvurl|u\.cntv)\.cn/[a-z0-9]+\$#sim", $v))
                {
                    
                }
                elseif (preg_match("#^https?://.+\$#sim", $v))
                {
                    //普通链接，转成短链接
                    $links[] = $v;
                }
            }

            $arrNode[] = $v;
        }
        $links =array_unique($links);
        $newText = '';
        if (!empty($links))
        {
            $sUrl = array();
            //同一进程中，不重复取短链
            foreach($links as $key=>$link)
            {
                $s = DCore_ProCache::get($link);
                if(!empty($s))
                {
                    $sUrl[] = array(
                        "url_long"=>$link,
                        "url_short"=>$s
                    );
                    unset($links[$key]);
                }
            }
            $sUrl = array_merge($sUrl, self::shortUrl($links));
            $tmp = array();
            foreach ($sUrl as $v)
            {
                $lk = strtolower($v['url_long']);
                $tmp[$lk] = preg_replace("#http://(?:sinaurl|t)\.cn/#si", OAPI_CNTV_SURL, $v['url_short']);
                //$tmp[$lk] = $v['url_short'];
                $linkmap[$v['url_short']] = $lk;
                //设置进程内缓存
                DCore_ProCache::set($lk, $v['url_short']);
            }

            foreach ($arrNode as $node)
            {
                $lk = strtolower($node);
                $newText .= isset($tmp[$lk]) ? $tmp[$lk] : $node;
            }
        }
        else
        {
            $newText = implode('', $arrNode);
        }

        return $newText;
    }

    /**
     * 长链接转换为短链接
     * 
     * @param mixed $url
     * @param 是否返回丰富的结果，默认为 true 
     */
    public static function shortUrl($longUrl)
    {
        if (empty($longUrl))
        {
            return array();
        }
        if (!is_array($longUrl))
        {
            $longUrls = array($longUrl);
        }
        else
        {
            $longUrls = $longUrl;
        }
        //var_export($longUrls);

        $tmpArr = array();

        foreach ($longUrls as $url)
        {
            $url = strtolower(substr($url, 0, 5)) . substr($url, 5);
            $tmpArr[] = 'url_long=' . urlencode($url);
        }

        //$url = sprintf('http://api.t.sina.com.cn/short_url/shorten.json?url_long=%s&source='.WB_AKEY,urlencode($url));
        $short_url_api = 'http://api.t.sina.com.cn/short_url/shorten.json?source=' . WB_AKEY;

        $url = $short_url_api . '&' . join('&', $tmpArr);
        //echo $url,"\n\n";
        $data = false;
        $nTry = 3;
        while ($nTry-- > 0)
        {
            if ($data = @file_get_contents($url))
            {
                $data = json_decode($data, TRUE);
                if (is_array($data) && isset($data[0]) && !isset($data['error_code']))
                {
                    return $data;
                }
            }
        }
        //echo $url,"\n\n";var_dump($data);;
        //解析失败，不解析
        if (!$data)
        {
            throw new Exception("Can't parse shorturl from $url");
        }

        return array();
    }

    public static function genWeiboHtml($arrBasicDatas, $gid)
    {
        if (empty($arrBasicDatas))
        {
            return '';
        }

        $html = '';

        foreach ($arrBasicDatas as $arrWeibo)
        {
            $text = self::formatText($arrWeibo['text'], $gid, $arrWeibo['linkmap']);

            $arrWeibo['text'] = $text;
            $html .= DCore_Template::render("weiqun_feed", $arrWeibo);
        }

        return $html;
    }

    public static function formatText($text, $gid, $linkmap = array())
    {
        static $emoticons = null;

        if (empty($text))
        {
            return $text;
        }

        // 获取表情
        if (is_null($emoticons))
        {
            $emoticons = self::getFaceRepPatterns();
        }

        $emo_tmp = array();
        foreach ($emoticons['search'] as $emo)
        {
            $emo_tmp[] = str_replace(array('[', ']'), array('\[', '\]'), $emo);
        }

        $pattern = array_merge($emo_tmp, array('&amp;', '&quot;', '&\#039;', '&lt;', '&gt;'));

        $arrPattern = array(
            '#[^#]+#', //话题
            '[a-z0-9\-_]*[a-z0-9]@(?:[a-z0-9-]+)(?:\.[a-z0-9-]+)+', //邮件地址 为了区分 @xxxx
            '@[\x{4e00}-\x{9fa5}0-9A-Za-z_\-]+', //@XXXXX
            'https?://[\w-\.]+[\w-]+(?:/[\w-_~!@#\$%\^&\*()\+-=\{\}\|\\\\:"\;\'<>\?,\./]*)?'//链接
        );

        $patterns = ';(' . implode('|', $arrPattern) . ');sium';
        $arrMatches = preg_split($patterns, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $newText = '';

        foreach ($arrMatches as $i => $v)
        {
            $v = htmlspecialchars($v);

            if ($i % 2)
            {
                if (substr($v, 0, 1) . substr($v, -1, 1) == '##')
                {
                    $tagname = substr($v, 1, strlen($v) - 2);
                    $tagname = urlencode($tagname);
                    $newText .= ' <a href="/topic_detail.php?gid='.$gid.'&tagname='.$tagname.'">' . $v . '</a> ';
                }
                elseif (substr($v, 0, 1) == '@')
                {
                    $newText .= ' <a target="_blank" href="http://' . CNTV_WEIBO_HOST . '/name/' . htmlspecialchars(substr($v, 1)) . '">' . $v . '</a> ';
                }
                elseif (preg_match("#^http://(?:cntvurl|u\.cntv)\.cn/[a-z0-9]+\$#sim", $v))
                {
                    $title = $v;
                    $link = $v;      
                  
                    $key = str_replace("u.cntv.cn", "t.cn", $v);
                     
                    if(isset($linkmap[$key]['title']))
                    {
                        $title = $linkmap[$key]['title'];
                        $link = $linkmap[$key]['link'];
                    } 
                    if(isset($linkmap[$key]))
                    {
                        $title = $linkmap[$key];
                        $link = $linkmap[$key];
                    }
                     
                  //  print_r($linkmap);
                    if(isset($linkmap[$key]))
                    {
                        $newText .= ' <a title="' . $title . '" href="' . $link . '" target="_blank">' . $v . '</a> ';
                    }
                    else
                    {
                        $newText .= ' <a title="' . $v . '" href="' . $v . '" target="_blank">' . $v . '</a> ';
                    }
                }
                else
                {
                    $newText.= $v;
                }
            }
            else
            {
                $newText.= $v;
            }
        }

        $text = $newText;
        //替换表情
        if ($emoticons)
        {
            $search_em = &$emoticons['search'];
            $replace_em = &$emoticons['replace'];

            //if (!empty($search_em)) {
            $text = str_replace($search_em, $replace_em, $text);
            //}
        }

        return $text;
    }

    private static function getFaceRepPatterns()
    {
        $rs = self::getEmotions();

        $result = array(
            'search' => array(),
            'replace' => array()
        );

        if (!empty($rs) && is_array($rs))
        {
            foreach ($rs as $face)
            {
                if ($face['type'] == 'face')
                {
                    array_push($result['search'], $face['phrase']);
                    array_push($result['replace'], '<img src="' . $face['url'] . '"/>');
                }
            }
        }

        return $result;
    }

    public static function getEmotions()
    {
        $faceFile = dirname(__FILE__) . '/face_data.php';
        if (file_exists($faceFile))
        {
            $data = @include $faceFile;
            if (!empty($data) && is_array($data))
            {
                return $data;
            }
            else
            {
                return false;
            }
        }
    }

}
