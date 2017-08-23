<?php

/**
 * @author unkown
 * @package include
 * 
 * 
 */
class DUtil_ShowRed
{

    static function html_showRedKeyEx_new($srcstr, $keys, $prefix = "<font color=red>", $suffix = "</font>")
    {
        $sa = explode("<", $srcstr);
        $count = count($sa);
        $redstr = "";
        for ($i = 0; $i < $count; $i++)
        {
            if ($i == 0)
            {
                $redstr .= DUtil_ShowRed::showRedKeyEx_new($sa[$i], $keys, $prefix, $suffix);
            }
            else
            {
                $pos = strpos($sa[$i], ">");
                if ($pos === false)
                {
                    $redstr .= "<" . $sa[$i];
                }
                else
                {
                    $redstr .= "<" . substr($sa[$i], 0, $pos + 1);
                    $redstr .= DUtil_ShowRed::showRedKeyEx_new(substr($sa[$i], $pos + 1), $keys, $prefix, $suffix);
                }
            }
        }
        return $redstr;
    }

    static function cmpfunc($first, $second)
    {
        $len1 = strlen($first);
        $len2 = strlen($second);

        if ($len1 == $len2)
        {
            return 0;
        }

        return ($len1 > $len2) ? -1 : 1;
    }

    static function showRedKeyEx_new($content, $keyword, $prefix = "<font color=red>", $suffix = "</font>")
    {
        $keywords = explode(' ', trim($keyword));

        // 关键字长的在前面
        usort($keywords, "DUtil_ShowRed::cmpfunc");

        $replace = array();
        foreach ($keywords as $words)
        {
            $replace[] = $prefix . $words . $suffix;
        }

        return str_ireplace($keywords, $replace, $content);
    }

    static function html_showRedKeyEx($srcstr, $keys, $prefix = "<font color=red>", $suffix = "</font>")
    {
        //该方法可以废弃了，打开下面的注释废弃
//                return DUtil_ShowRed::html_showRedKeyEx_new($srcstr, $keys, $prefix, $suffix);
        $sa = explode("<", $srcstr);
        $count = count($sa);
        $redstr = "";
        for ($i = 0; $i < $count; $i++)
        {
            if ($i == 0)
            {
                $redstr .= DUtil_ShowRed::showRedKeyEx($sa[$i], $keys, $prefix, $suffix);
            }
            else
            {
                $pos = strpos($sa[$i], ">");
                if ($pos === false)
                {
                    $redstr .= "<" . $sa[$i];
                }
                else
                {
                    $redstr .= "<" . substr($sa[$i], 0, $pos + 1);
                    $redstr .= DUtil_ShowRed::showRedKeyEx(substr($sa[$i], $pos + 1), $keys, $prefix, $suffix);
                }
            }
        }
        return $redstr;
    }

    static function showRedKeyEx($srcstr, $keys, $prefix = "<font color=red>", $suffix = "</font>")
    {
        //该方法可以废弃了，打开下面的方法废弃
//                return DUtil_ShowRed::showRedKeyEx_new($srcstr, $keys, $prefix, $suffix);
        $pos = 0;
        $keys = strtolower($keys);
        $len = strlen($keys);
        do
        {
            $idx = strpos($keys, " ", $pos);
            if ($idx === FALSE)//只有一个或最后一个关键字
            {
                $qstr = substr($keys, $pos);
                $srcstr = DUtil_ShowRed::matchAndRedKeyEx($srcstr, $qstr, $prefix, $suffix);
                return $srcstr;
            }
            else
            {
                //有多个关键字
                $qstr = substr($keys, $pos, $idx - $pos); //取出关键字
            }

            //标红该关键字
            $srcstr = DUtil_ShowRed::matchAndRedKeyEx($srcstr, $qstr, $prefix, $suffix);
            $pos = $idx + 1;
        }
        while ($pos < $len);

        return $srcstr;
    }

    function matchAndRedKeyEx($srcstr, $key, $prefix = "<font color=red>", $suffix = "</font>")
    {
        $newstr = "";
        $length = strlen($srcstr);
        $keylen = strlen($key);

        if (0 == $keylen)
        {
            return $srcstr;
        }

        $infix = 0;
        if (strpos($prefix, $key) || strpos($suffix, $key))
        {
            $infix = 1;
        }

        $j = 0;
        $pos = 0;
        $start = 0;

        for ($i = 0; $j < $keylen && $i < $length;)
        {
            //判断关键字是否汉字字符开头
            if (ord($key[$j]) > 0x80)
            {
                //关键字汉字字符开头
                if (ord($srcstr[$i]) > 0x80)
                {
                    //$srcstr汉字字符开头
                    if ($key[$j] == $srcstr[$i] && $key[$j + 1] == $srcstr[$i + 1])
                    {
                        //匹配，向后遍历
                        $pos = $i;
                        $j+=2;
                        $i+=2;

                        for (; $j < $keylen && $i < $length; $j++)
                        {
                            if ($key[$j] == $srcstr[$i] || $key[$j] == strtolower($srcstr[$i]))//case-insensitive 
                            {
                                if (ord($key[$j]) > 0x80)
                                {
                                    if ($key[$j + 1] == $srcstr[$i + 1])
                                    {
                                        $j++;
                                        $i+=2;
                                    }
                                    else
                                    {
                                        //不匹配，复位
                                        $j = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                    $i++;
                                }
                            }
                            else
                            {
                                //不匹配，复位
                                $j = 0;
                                break;
                            }
                        }

                        if ($j == $keylen)
                        {
                            //匹配，复位$j, 搜索下一个关键词
                            $dstred = substr($srcstr, $pos, $keylen);
                            $reded = DUtil_ShowRed::inRedString($srcstr, $pos, $keylen, $prefix, $suffix);
                            if ($reded)
                            {
                                $j = 0;
                            }
                            else
                            {
                                if (strpos($prefix, $dstred) || strpos($suffix, $dstred))
                                {
                                    $infix = 1;
                                }
                                if ($infix == 1)
                                {
                                    $nn = DUtil_ShowRed::infix($srcstr, $pos, $keylen, $prefix, $suffix);

                                    if ($nn == 2 || $nn == -2)
                                    {
                                        $j = 0;
                                    }
                                    else
                                    {
                                        $newstr .= substr($srcstr, $start, $pos - $start);
                                        $newstr .= $prefix;
                                        $newstr .= $dstred;
                                        $newstr .= $suffix;
                                        $j = 0;
                                        $start = $pos + $keylen;
                                    }
                                }
                                else
                                {
                                    $newstr .= substr($srcstr, $start, $pos - $start);
                                    $newstr .= $prefix;
                                    $newstr .= $dstred;
                                    $newstr .= $suffix;
                                    $j = 0;
                                    $start = $pos + $keylen;
                                }
                            }
                        }
                        $j = 0;
                    }
                    else
                    {
                        //不匹配，向后遍历
                        $i+=2;
                    }
                }
                else //$srcstr非汉字字符开头
                {
                    $i++;
                }
            }
            else //关键字非汉字开头
            {
                if (ord($srcstr[$i]) <= 0x80)
                {
                    //$srcstr非汉字字符开头
                    if ($key[$j] == $srcstr[$i] || $key[$j] == strtolower($srcstr[$i]))
                    {
                        //$srcstr非汉字字符开头, 匹配，向后遍历
                        $pos = $i;
                        $j++;
                        $i++;

                        for (; $j < $keylen && $i < $length; $j++)
                        {
                            //不区分大小写
                            if ($key[$j] == $srcstr[$i] || $key[$j] == strtolower($srcstr[$i]))
                            {
                                if (ord($key[$j]) > 0x80)
                                {
                                    if ($key[$j + 1] == $srcstr[$i + 1])
                                    {
                                        $j++;
                                        $i+=2;
                                    }
                                    else
                                    {
                                        //不匹配，复位
                                        $j = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                    $i++;
                                }
                            }
                            else
                            {
                                //不匹配，复位
                                $j = 0;
                                break;
                            }
                        }

                        if ($j == $keylen)
                        {
                            //匹配，复位
                            $dstred = substr($srcstr, $pos, $keylen);
                            $reded = DUtil_ShowRed::inRedString($srcstr, $pos, $keylen, $prefix, $suffix);
                            if ($reded)
                            {
                                $j = 0;
                            }
                            else
                            {
                                if (strpos($prefix, $dstred) || strpos($suffix, $dstred))
                                {
                                    $infix = 1;
                                }
                                if ($infix == 1)
                                {
                                    $nn = DUtil_ShowRed::infix($srcstr, $pos, $keylen, $prefix, $suffix);
                                    if ($nn == 2 || $nn == -2)
                                    {
                                        $j = 0;
                                    }
                                    else
                                    {
                                        $newstr .= substr($srcstr, $start, $pos - $start);
                                        $newstr .= $prefix;
                                        $newstr .= $dstred;
                                        $newstr .= $suffix;
                                        $j = 0;
                                        $start = $pos + $keylen;
                                    }
                                }
                                else
                                {
                                    $newstr .= substr($srcstr, $start, $pos - $start);
                                    $newstr .= $prefix;
                                    $newstr .= $dstred;
                                    $newstr .= $suffix;
                                    $j = 0;
                                    $start = $pos + $keylen;
                                }
                            }
                        }
                        $j = 0;
                    }
                    else
                    {
                        $i++;
                    }
                }
                else
                {
                    //$srcstr汉字字符开头,不匹配，向后遍历
                    $i+=2;
                }
            }
        }

        $newstr .= substr($srcstr, $start);
        return $newstr;
    }

    function inRedString($srcstr, $pos, $keylen, $prefix = "<font color=red>", $suffix = "</font>")
    {
        $prefixlen = strlen($prefix);
        $suffixlen = strlen($suffix);

        $pre2 = $pre = substr($srcstr, 0, $pos);
        $suf = substr($srcstr, $pos);

        $prelastprefixpos = strpos($pre, $prefix);
        if ($prelastprefixpos >= 0)
        {
            $pre = substr($pre, $prelastprefixpos + $prefixlen);
            while ($p = strpos($pre, $prefix))
            {
                $prelastprefixpos += $prefixlen + $p;
                $pre = substr($pre, $prelastprefixpos + $prefixlen);
            }
        }

        $prelastsuffixpos = strpos($pre2, $suffix);
        if ($prelastsuffixpos >= 0)
        {
            $pre2 = substr($pre2, $prelastsuffixpos + $suffixlen);
            while ($p = strpos($pre2, $suffix))
            {
                $prelastsuffixpos += $suffixlen + $p;
                $pre2 = substr($pre2, $prelastsuffixpos + $suffixlen);
            }
        }

        $suffirstprefixpos = strpos($suf, $prefix);
        $suffirstsuffixpos = strpos($suf, $suffix);

        if ($prelastprefixpos !== false && (($prelastsuffixpos >= 0 && $prelastprefixpos > $prelastsuffixpos) || (false === $prelastsuffixpos)) && $suffirstsuffixpos !== false && (($suffirstprefixpos >= 0 && $suffirstprefixpos > $suffirstsuffixpos) || (false === $suffirstprefixpos)))
        {
            return 1;
        }
        return 0;
    }

    function infix($srcstr, $pos, $keylen, $prefix = "<font color=red>", $suffix = "</font>")
    {
        $fixlen = strlen($prefix);
        $in = 0;
        for ($m = 0; ($pos - $m >= 0) && $m < $fixlen; $m++)
        {
            if ($srcstr[$pos - $m] == '<')
            {
                //在<>内
                $in+=1;
                break;
            }
            if ($srcstr[$pos - $m] == '>')
            {
                //不在<>内
                $in-=1;
                break;
            }
        }
        $len = strlen($srcstr);
        for ($m = 0; ($pos + $keylen + $m < $len) && $m < $fixlen; $m++)
        {
            if ($srcstr[$pos + $keylen + $m] == '<')
            {
                //不在<>内
                $in-=1;
                break;
            }
            if ($srcstr[$pos + $keylen + $m] == '>')
            {
                //在<>内
                $in+=1;
                break;
            }
        }
        return $in;
    }

}

?>