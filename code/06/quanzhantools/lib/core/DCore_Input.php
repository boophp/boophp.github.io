<?php

/**
 * 
 * 文件名称：DCore_Input.php
 * 摘    要：检测和规范输入数据的php类

 */
define('TYPE_NOCLEAN', 0); // 不做处理
define('TYPE_INT', 1); // 转换成integer
define('TYPE_UINT', 2); // 转换成无符号integer
define('TYPE_NUM', 3); // 转换成number
define('TYPE_UNUM', 4); // 转换成无符号 number
define('TYPE_STR', 5); // 转换成string，并去除两边的空格
define('TYPE_NOTRIM', 6); // 转换成string，保留空格
define('TYPE_ARRAY', 7); // 转换成array
define('TYPE_FILE', 8); // 转换成file
define('TYPE_UTF8', 9); // UTF8转换为GBK
define('TYPE_HTML', 10); // HTML提交
define('TYPE_HTML_UTF8', 11); // UTF8转换为GBK，HTML提交
define('TYPE_RICHHTML', 12); // HTML提交
define('TYPE_RICHHTML_UTF8', 13); // UTF8转换为GBK，HTML提交
define('TYPE_STR_GB', 14); // 转换成string，并去除两边的空格，不进行转码
define('TYPE_NOTRIM_GB', 15); // 转换成string，并去除两边的空格，不进行转码
define('TYPE_FILE_GB', 16); // 转换成file，不进行转码
define('TYPE_ARRAY_GB', 17); // 转换成array，不进行转码
define('TYPE_LARGE_HTML', 18); // 大文本 HTML 提交(去除标记更严格）
define('TYPE_LARGE_HTML_UTF8', 19); //大文本 UTF8转换为GBK，HTML提交(去除标记更严格）
define('TYPE_LARGE_RICHHTML', 20); // 大文本 HTML 提交
define('TYPE_LARGE_RICHHTML_UTF8', 21); // 大文本 UTF8转换为GBK，HTML提交

class DCore_Input
{
    const TYPE_UINT = TYPE_UINT;
    const TYPE_INT = TYPE_INT;
    const TYPE_STR = TYPE_STR;
    const TYPE_STR_GB = TYPE_STR_GB;

    static function set_superglobal($source)
    {
        $superglobal = array("g" => "_GET", "p" => "_POST", "r" => "_REQUEST", "c" => "_COOKIE", "s" => "_SERVER", "e" => "_ENV", "f" => "_FILES");
        return $superglobal[$source];
    }

    static function clean($source, $varname, $vartype = TYPE_NOCLEAN)
    {
        //print_r(func_get_args());
        $sg = &$GLOBALS[DCore_Input::set_superglobal($source)];

        $var = isset($sg[$varname]) ? $sg[$varname] : "";
        if (is_array($var))
        {
            foreach ($var as $tmp)
            {
                $tmp = strtolower($tmp);
                if (strpos($tmp, "<script") !== false
                    || strpos($tmp, "<iframe") !== false)
                {
                    $ip = DUtil_Ip::getIP();
                    //TODO
                    //addSysDebugLog($varname."\t".$ip."\t".substr($tmp, 0, 128)."\t".getenv("REQUEST_URI"));			
                    break;
                }
            }
        }
        else
        {
            $tmp = strtolower($var);
            if (strpos($tmp, "<script") !== false
                || strpos($tmp, "<iframe") !== false)
            {
                $ip = DUtil_Ip::getIP();
                //TODO
                //addSysDebugLog($varname."\t".$ip."\t".substr($tmp, 0, 128)."\t".getenv("REQUEST_URI"));			
            }
        }
        return DCore_Input::do_clean($var, $vartype);
    }

    static function my_input_clear()
    {
        if (get_magic_quotes_gpc())
        {
            DCore_Input::stripslashes_deep($_GET);
            DCore_Input::stripslashes_deep($_POST);
            DCore_Input::stripslashes_deep($_COOKIE);
            DCore_Input::stripslashes_deep($_REQUEST);
        }
        //	set_magic_quotes_runtime(0);
    }

    static function checkParam($param, $value, $len = 0, $null = false)
    {
        if (!$null && !$value)
        {
            return "<b>" . $param . "</b> 忘写了？";
        }
        if (strlen($value) < $len)
        {
            return "<b>" . $param . "</b>  是不是写得不对？再多写一点吧！";
        }
        return "";
    }

    static function stripslashes_deep(&$value)
    {
        if (is_array($value))
        {
            foreach ($value AS $key => $val)
            {
                if (is_string($val))
                {
                    $value[$key] = stripslashes($val);
                }
                else if (is_array($val))
                {
                    DCore_Input::stripslashes_deep($value[$key]);
                }
            }
        }
        else if (is_string($value))
        {
            $value = stripslashes($value);
        }
    }

    static function &do_clean(&$data, $type)
    {
        switch ($type)
        {
            case TYPE_INT:
                {
                    $data = intval($data);
                    break;
                }
            case TYPE_UINT:
                {
                    $data = ($data = intval($data)) < 0 ? 0 : $data;
                    break;
                }
            case TYPE_NUM:
                {
                    $data += 0;
                    break;
                }
            case TYPE_UNUM:
                {
                    $data = ($data += 0) < 0 ? 0 : $data;
                    break;
                }
            case TYPE_STR:
                {
                    //$convert = new DUtil_Convert();
                    //$data = DUtil_Convert::filter_PUZ($data);
                    //$data = $convert->junk2gbk($data);
                    $data = trim(strval($data));
                    break;
                }
            case TYPE_NOTRIM:
                {
                    $convert = new DUtil_Convert();
                    $data = $convert->junk2gbk($data);
                    $data = strval($data);
                    break;
                }
            case TYPE_ARRAY:
                {
                    $data = (is_array($data)) ? $data : array();
                    $convert = new DUtil_Convert();
                    foreach ($data as $key => $value)
                    {
                        $data[$key] = $convert->junk2gbk($value);
                    }
                    break;
                }
            case TYPE_FILE:
                {
                    if (!is_array($data))
                    {
                        $data = array(
                            'name' => '',
                            'type' => '',
                            'size' => 0,
                            'tmp_name' => '',
                            'error' => UPLOAD_ERR_NO_FILE,
                        );
                    }
                    $convert = new DUtil_Convert();
                    $data["name"] = $convert->junk2gbk($data["name"]);
                    break;
                }
            case TYPE_UTF8:
                {
                    $convert = new DUtil_Convert();
                    $data = $convert->junk2gbk($data);
                    break;
                }
            case TYPE_HTML:
            case TYPE_HTML_UTF8:
            case TYPE_RICHHTML:
            case TYPE_RICHHTML_UTF8:
            case TYPE_LARGE_HTML:
            case TYPE_LARGE_HTML_UTF8:
            case TYPE_LARGE_RICHHTML:
            case TYPE_LARGE_RICHHTML_UTF8:
                {
                    $convert = new DUtil_Convert();
                    $data = $convert->junk2gbk($data);
                    if ($_REQUEST["texttype"] == "plain")
                    {
                        $data = nl2br(htmlspecialchars($data));
                    }
                    else
                    {
                        $p = new CHtmlParse();
                        if (TYPE_HTML == $type
                            || TYPE_HTML_UTF8 == $type
                            || TYPE_LARGE_HTML == $type
                            || TYPE_LARGE_HTML_UTF8 == $type)
                        {
                            $data = $p->parse($data, "msg");
                        }
                        else
                        {
                            $data = $p->parse($data);
                        }
                    }
                    if (TYPE_HTML == $type
                        || TYPE_HTML_UTF8 == $type
                        || TYPE_RICHHTML == $type
                        || TYPE_RICHHTML_UTF8 == $type)
                    {
                        if (strlen($data) > 65535)
                        {
                            $p = new CHtmlParse();
                            list($data, $cut) = $p->getAbstract($data, 6000);
                        }
                    }
                    break;
                }
            case TYPE_STR_GB:
                {
                    $data = trim(strval($data));
                    break;
                }
            case TYPE_NOTRIM_GB:
                {
                    $data = strval($data);
                    break;
                }
            case TYPE_FILE_GB:
                {
                    if (!is_array($data))
                    {
                        $data = array(
                            'name' => '',
                            'type' => '',
                            'size' => 0,
                            'tmp_name' => '',
                            'error' => UPLOAD_ERR_NO_FILE,
                        );
                    }
                    break;
                }
            case TYPE_ARRAY_GB:
                {
                    $data = (is_array($data)) ? $data : array();
                    break;
                }
        }
        return $data;
    }

    function isEmail($email)
    {
        return 0 != preg_match("/^[A-Za-z0-9+]+[A-Za-z0-9\.\_\-+]*@([A-Za-z0-9\-]+\.)+[A-Za-z0-9]+$/", $email);
    }

    static function isDomain($domain)
    {
        return 0 != preg_match("/^[A-Za-z0-9\-]+$/", $domain);
    }

}

//自动去掉addslashes()函数添加的标记
DCore_Input::my_input_clear();
?>