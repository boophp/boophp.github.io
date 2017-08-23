<?php

/** CUitls类，定义一些通用静态函数 * */
class DUtil_Base
{

    // 静态变量，保存类的 单例对象（Singleton 模式）
    protected static $arrInstances;

    /**
     * 得到某个类的单例对象
     *
     * @param string	$strClassName		类的名字
     *
     * @return object	返回该类的对象
     *
     *
     */
    public static function getInstance($strClassName)
    {

        if (empty(self::$arrInstances[$strClassName]))
        {
            if ($strClassName == 'DDb_Handle')
            {
                self::$arrInstances[$strClassName] = DDb_Handle::getInstance(DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME);
            }
            else
            {
                self::$arrInstances[$strClassName] = new $strClassName();
            }
        }

        return self::$arrInstances[$strClassName];
    }

    /**
     * 过滤字符串中 http 开头的完整域名
     *
     * @param string	$str	字符串，引用传递，当包含字符串时进行替换
     *
     */
    public static function filterHost(&$str)
    {

        static $strFilterFullHost = null;

        if (empty($strFilterFullHost))
        {
            $strFilterFullHost = 'http://' . WWW_HOST;
        }

        if (false !== strpos($str, $strFilterFullHost))
        {
            $str = str_replace($strFilterFullHost, '', $str);
        }
    }

    public static function getFuncPara($arr_src, $arr_para)
    {
        $arr_dst = array();
        foreach ($arr_para as $var_name => $var_type)
        {
            if (!isset($arr_src[$var_name]))
            {
                continue;
            }

            $var_value = $arr_src[$var_name];
            CInput::do_clean($var_value, $var_type);

            $arr_dst[$var_name] = $var_value;
        }

        return $arr_dst;
    }

    public static function compareArrayByField($a, $b, $field, $desc=0)
    {
        if ($a[$field] < $b[$field])
        {
            return $desc ? 1 : -1;
        }
        else if ($a[$field] > $b[$field])
        {
            return $desc ? -1 : 1;
        }
        else
        {
            return 0;
        }
    }

    public static function utf8_urldecode($str)
    {
        $str = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str));
        return html_entity_decode($str, null, 'UTF-8');
        ;
    }

    private static function long2str($v, $w)
    {
        $len = count($v);
        $n = ($len - 1) << 2;
        if ($w)
        {
            $m = $v[$len - 1];
            if (($m < $n - 3) || ($m > $n))
                return false;
            $n = $m;
        }
        $s = array();
        for ($i = 0; $i < $len; $i++)
        {
            $s[$i] = pack("V", $v[$i]);
        }
        if ($w)
        {
            return substr(join('', $s), 0, $n);
        }
        else
        {
            return join('', $s);
        }
    }

    private static function str2long($s, $w)
    {
        $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
        $v = array_values($v);
        if ($w)
        {
            $v[count($v)] = strlen($s);
        }
        return $v;
    }

    private static function int32($n)
    {
        while ($n >= 2147483648)
            $n -= 4294967296;
        while ($n <= -2147483649)
            $n += 4294967296;
        return (int) $n;
    }

    public static function xxtea_encrypt($str, $key)
    {
        if ($str == "")
        {
            //return ""; //为空也要继续，因为密码验证用到这个，有人的密码全是空格
        }
        $v = CUtils::str2long($str, true);
        $k = CUtils::str2long($key, false);
        if (count($k) < 4)
        {
            for ($i = count($k); $i < 4; $i++)
            {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;

        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = 0;
        while (0 < $q--)
        {
            $sum = CUtils::int32($sum + $delta);
            $e = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++)
            {
                $y = $v[$p + 1];
                $mx = CUtils::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) +
                        (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ CUtils::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $z = $v[$p] = CUtils::int32($v[$p] + $mx);
            }
            $y = $v[0];
            $mx = CUtils::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) +
                    (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ CUtils::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$n] = CUtils::int32($v[$n] + $mx);
        }
        return CUtils::long2str($v, false);
    }

    public static function xxtea_decrypt($str, $key)
    {
        if ($str == "")
        {
            return "";
        }
        $v = CUtils::str2long($str, false);
        $k = CUtils::str2long($key, false);
        if (count($k) < 4)
        {
            for ($i = count($k); $i < 4; $i++)
            {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;

        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = CUtils::int32($q * $delta);
        while ($sum != 0)
        {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--)
            {
                $z = $v[$p - 1];
                $mx = CUtils::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) +
                        (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ CUtils::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $y = $v[$p] = CUtils::int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = CUtils::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) +
                    (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ CUtils::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[0] = CUtils::int32($v[0] - $mx);
            $sum = CUtils::int32($sum - $delta);
        }
        return CUtils::long2str($v, true);
    }

    public static function _dispatchCall($capp, $name, $arguments)
    {
        switch (count($arguments))
        {
            case 0:
                return $capp->$name();
                break;
            case 1:
                return $capp->$name($arguments[0]);
                break;
            case 2:
                return $capp->$name($arguments[0], $arguments[1]);
                break;
            case 3:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2]);
                break;
            case 4:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;
            case 5:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
                break;
            case 6:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
                break;
            case 7:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
                break;
            case 8:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7]);
                break;
            case 9:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8]);
                break;
            case 10:
                return $capp->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8], $arguments[9]);
                break;
        }

        throw new DExcept_BaseException(0, "too many arguments");
    }

    // add by Liulikang (2010-09-25)
    function toUTF8($data)
    {
        if (is_array($data))
        {
            $return = array();
            foreach ($data as $key => $val)
            {
                $return[$key] = self::toUTF8($val);
            }
        }
        else
        {

            $return = iconv("GB18030", "UTF-8//IGNORE", $data);
        }
        return $return;
    }

    // add by Liulikang (2010-09-25)
    function toGB18030($data)
    {
        if (is_array($data))
        {
            $return = array();
            foreach ($data as $key => $val)
            {
                $return[$key] = self::toUTF8($val);
            }
        }
        else
        {

            $return = iconv("UTF-8", "GB18030//IGNORE", $data);
        }
        return $return;
    }

    static function getEmptyResult()
    {
        $ret = new Space_QResult();
        $ret->affectedRowNumber = 0;
        $ret->insertId = 0;
        $ret->fields = array();
        $ret->rows = array();
        return new CDBResult($ret, 0);
    }

    public static function htmlspecialchars($var)
    {
        if (is_array($var))
        {
            return array_map(array(self, "htmlspecialchars"), $var);
        }
        else
        {
            return htmlspecialchars($var);
        }
    }

    public static function doAdminLogCryption($string, $operation, $key='')
    {
        $string = str_replace(" ", "+", $string);
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++)
        {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result.=chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D')
        {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8))
            {
                return substr($result, 8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            return str_replace('=', '', base64_encode($result));
        }
    }

    public static function getSelectHtml($name, $opts, $vals, $selval, $chgFunc="", $str="", $needempty=false, $style="")
    {
        $html = $str . ' <select id="' . $name . '" name="' . $name . '" style="' . $style . '" onchange="' . $chgFunc . '">';
        if ($needempty)
        {
            $html .= '<option value="">请选择</option>';
        }
        foreach ($opts as $i => $opt)
        {
            $chk = "";
            if ($vals[$i] == $selval)
            {
                $chk = "selected";
            }
            $html .= '<option value="' . $vals[$i] . '" ' . $chk . '>' . $opt . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    static function getPagehtml($start, $num, $total, $app)
    {
        $pagenum = 5;
        $curpage = $start / $num + 1;
        $totalpage = ceil($total / $num);
        $minpage = max($curpage - round($pagenum / 2) + 1, 1);
        $maxpage = min($minpage + $pagenum - 1, $totalpage);

        if ($totalpage <= 1)
        {
            return "";
        }

        $pagehtml = "";
        if ($curpage > 1)
        {
            $pagehtml .= "<a href=\"" . $app . "0" . "\" class=\"pg_first\"  onfocus=\"this.blur();\">首页</a> ";
            $pagehtml .= "<a href=\"" . $app . (($curpage - 2) * $num) . "\" class=\"pg_pre\" onfocus=\"this.blur();\">&#171;　上一页</a>　　";
        }
        else
        {
//			$pagehtml .= "&#171;　上一页　　";
        }

        for ($i = $minpage; $i <= $maxpage; $i++)
        {
            if ($i != $curpage)
            {
                $pagehtml .= "<a href=\"" . $app . (($i - 1) * $num) . "\"  onfocus=\"this.blur();\">" . $i . "</a>";
            }
            else
            {
                $pagehtml .= "<span>" . $i . "</span>";
            }
        }

        if ($curpage < $totalpage)
        {
            $pagehtml .= "　　<a href=\"" . $app . ($curpage * $num) . "\" class=\"pg_next\" onfocus=\"this.blur();\">下一页　&#187;</a>";
            $pagehtml .= " <a href=\"" . $app . (($totalpage - 1) * $num) . "\" class=\"pg_end\" onfocus=\"this.blur();\">末页</a>";
        }
        else
        {
//			$pagehtml .= "　　下一页　&#187;";
        }
        return $pagehtml;
    }
    //ajax区域js刷新
    static function getJsPagehtml($start, $num, $total, $func)
    {
        $pagenum = 5;
        $curpage = $start / $num + 1;
        $totalpage = ceil($total / $num);
        $minpage = max($curpage - round($pagenum / 2) + 1, 1);
        $maxpage = min($minpage + $pagenum - 1, $totalpage);

        if ($totalpage <= 1)
        {
            return "";
        }

        $pagehtml = "";
        if ($curpage > 1)
        {
            $pagehtml .= "<a href=\"javascript:;\" onclick=\"" . str_replace("{_START_}","0",$func) . "\" class=\"pg_first\"  onfocus=\"this.blur();\">首页</a> ";
            $pagehtml .= "<a href=\"javascript:;\" onclick=\"" . str_replace("{_START_}",(($curpage-2) * $num),$func) . "\" class=\"pg_pre\" onfocus=\"this.blur();\">&#171;　上一页</a>　　";
        }
        else
        {
//			$pagehtml .= "&#171;　上一页　　";
        }

        for ($i = $minpage; $i <= $maxpage; $i++)
        {
            if ($i != $curpage)
            {
                $pagehtml .= "<a href=\"javascript:;\" onclick=\"" . str_replace("{_START_}",(($i-1) * $num),$func) . "\"  onfocus=\"this.blur();\">" . $i . "</a>";
            }
            else
            {
                $pagehtml .= "<span>" . $i . "</span>";
            }
        }

        if ($curpage < $totalpage)
        {
            $pagehtml .= "　　<a href=\"javascript:;\" onclick=\"" . str_replace("{_START_}",($curpage * $num),$func) . "\" class=\"pg_next\" onfocus=\"this.blur();\">下一页　&#187;</a>";
            $pagehtml .= " <a href=\"javascript:;\" onclick=\"" . str_replace("{_START_}",(($totalpage-1) * $num),$func) . "\" class=\"pg_end\" onfocus=\"this.blur();\">末页</a>";
        }
        else
        {
//			$pagehtml .= "　　下一页　&#187;";
        }
        return $pagehtml;
    }

    static function cookieset($k, $v, $expire=0)
    {
        $pre = substr(md5($_SERVER['HTTP_HOST']), 0, 4);
        $k = "{$pre}_{$k}";
        if ($expire == 0)
        {
            $expire = time() + 365 * 86400;
        }
        else
        {
            $expire += time();
        }
        setCookie($k, $v, $expire, '/');
    }

    static function cookiedel($k='')
    {
        if ('' == $k)
        {
            $pre = substr(md5($_SERVER['HTTP_HOST']), 0, 4);
            $k = "{$pre}_" . DLogin_Func::$cookie_name;
        }
        setCookie($k, '', time() - 3600, '/');
    }

    static function cookieget($k)
    {
        $pre = substr(md5($_SERVER['HTTP_HOST']), 0, 4);
        $k = "{$pre}_{$k}";
        return strval($_COOKIE[$k]);
    }

    static function redirect($url=null)
    {

        $url = $url ? $url : $_SERVER['HTTP_REFERER'];
        $url = $url ? $url : '/';
        //开发阶段的临时处理
        if ($_SERVER['HTTP_HOST'] == 'localhost')
        {
            $url = "/maiyou/" . $url;
        }
        header("Location: {$url}");
        exit;
    }

    static function outputJson($data, $type='eval')
    {
        $type = strtolower($type);
        $allow = array('eval', 'alert', 'updater', 'dialog', 'mix', 'refresh');
        if (false == in_array($type, $allow))
            return false;
        self::_outputJson(array('data' => $data, 'type' => $type,));
    }

    private static function error($error=0)
    {
        return array('error' => intval($error),);
    }

    private static function _outputJson($data=null, $error=0)
    {
        $result = self::error($error);

        if (null !== $data)
        {
            $result['data'] = $data;
        }
        // print_r($result);
        $result = (json_encode($result));
        echo $result;
        exit;
    }

    /**
     * 将外站链接转换为带统计的链接
     */
    public static function getStatUrl($url, $midurl = "")
    {
        if (!$midurl)
        {
            $midurl = "/interface/redirect.php";
        }
        $url = $midurl . "?url=" . urlencode($url);

        return $staturl . "&sig=" . self::genStaturlAuthKey($url);
    }

    /**
     * 增加外站链接验证
     */
    public static function genStaturlAuthKey($url)
    {
        return md5(LINK_SYS_CODE . $url);
    }

    /**
     * 把数字索引的二维数组结果集，转换为以某个字段为索引的数组
     *
     * @param string	$strKey		字段名
     *
     * @return array
     *
     */
    public static function toKeyIndexed($arrResult, $strKey)
    {

        $arrRet = array();

        foreach ($arrResult as $val)
        {

            // 有这个字段才设置
            if (isset($val[$strKey]))
            {

                $key = $val[$strKey];

                $arrRet[$key] = $val;
            }
        }

        return $arrRet;
    }
    
   

}

?>
