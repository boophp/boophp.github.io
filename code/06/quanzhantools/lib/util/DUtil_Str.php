<?php

/**

 * 字符串处理

 *

 * @package base

 * @author unknown

 */
class DUtil_Str
{

    public static $ubb_cnum;
    public static $ubb_arrcode;
    public static $forbidword = array();
    public static $j2f = array();
    //邮件地址是否合法

    public static $focusemail = array();
    public static $friendwebsite = array();
    public static $repasteforbidword = array();
    public static $repasteforbidurl = array();
    public static $forbiddiaryword = array();
    // 检查个性域名是否包含敏感词

    public static $tiny_url = array();
    public static $firstname_single = array();
    public static $firstname_double = array();
    public static $f2j = array();
    public static $virtualman_filterword = array();

    static function simpleAbstract($val, $len = 25)
    {
        if ((strlen($val) > 2 * len) && ( $len > 0))
        {
            $val = substr($val, 0, $len) . "..." . substr($val, (-1) * $len);
        }
        return $val;
    }

    static function simpleAbstractEx($val, $len = 25)
    {
        if ((strlen($val) > 2 * len) && ( $len > 0))
        {
            $val = DUtil_Str::subString($val, $len * 2, "...");
        }
        return $val;
    }

    public static function substr($string, $offeset, $length=0, $tail="...", $encode="utf-8")
    {
        if (0 == $length)
            $length = mb_strlen($string, $encode);
        $str = mb_substr($string, $offeset, $length, $encode);

        if (mb_strlen($str, $encode) < mb_strlen($string, $encode))
            $str .= $tail;

        return $str;
    }

    function loadKeyword($varname)
    {

        if (empty(self::$$varname))
        {

            $filename = DATA_PATH . "/keyword/" . $varname . ".txt";

            $content = file_get_contents($filename);

            $fn_list = explode("\n", $content);

            foreach ($fn_list as $fn)
            {

                $fn = trim($fn);

                if (strlen($fn) == 0)
                {

                    continue;
                }

                array_push(self::$$varname, $fn);
            }
        }
    }

    function isKeyword($varname, $word, &$fword)
    {

        DUtil_Str::loadKeyword($varname);

        $forbid = false;

        mb_internal_encoding("GBK");

        $word = mb_strtolower($word);

        foreach (self::$$varname as $keyword)
        {

            if (false !== mb_strpos($word, mb_strtolower($keyword)))
            {

                $fword = $keyword;

                $forbid = true;

                break;
            }
        }

        return $forbid;
    }

    //用于
    function isKeyword2($varname, $word, $level)
    {
        DUtil_Str::loadKeyword($varname, $level);
        $forbid = false;
        mb_internal_encoding("GBK");
        $word = mb_strtolower($word);
        foreach (self::$$varname as $keyword)
        {
            list($keyword, $klevel) = explode("\t", $keyword);
            $keyword = trim($keyword);
            $klevel = (int) $klevel;
            if (false !== mb_strpos($word, mb_strtolower($keyword)))
            {
                if ($klevel == $level)
                {
                    $fword = $keyword;
                    $forbid = $fword;
                    break;
                }
            }
        }
        return $forbid;
    }

    function replaceKeyword($varname, &$word)
    {

        DUtil_Str::loadKeyword($varname);

        $forbid = false;

        mb_internal_encoding("GB2312");

        foreach (self::$$varname as $keyword)
        {

            if (false !== mb_strpos($word, $keyword))
            {

                $forbid = true;

                $starstr = "";

                for ($i = 0; $i < strlen($keyword); $i++)
                {

                    $starstr .= "*";
                }

                $word = mb_ereg_replace($keyword, $starstr, $word);
            }
        }

        return $forbid;
    }

    /**

     * 检查个性域名是否包含敏感词

     *

     * Liulikang	(2010-03-12)

     *

     */
    function isForbidWord($word, &$fword)
    {

        return DUtil_Str::isKeyword("forbidword", $word, $fword);
    }

    function arr2enc($arr)
    {

        $str = implode(" ", $arr);

        return urlencode($str);
    }

    static function saveCodeArea($match)
    {
        self::$ubb_cnum++;
        self::$ubb_arrcode[self::$ubb_cnum] = $match[0];
        return "[\tubbcodeplace_" . self::$ubb_cnum . "\t]";
    }

    static function getSizeName($match)
    {
        $arrSize = array('8pt', '10pt', '12pt', '14pt', '18pt', '24pt', '36pt');
        return '<span style="font-size:' . $arrSize[$match[1] - 1] . ';">';
    }

    static function getImg($match)
    {
        $p1 = $match[1];
        $p2 = $match[2];
        $p3 = $match[3];
        $src = $match[4];
        $a = $p3 ? $p3 : ($p2 ? $p1 : '');
        return '<img src="' . $src . '"' . ($p2 ? ' width="' . $p1 . '" height="' . $p2 . '"' : '') . ($a ? ' align="' . $a . '"' : '') . ' />';
    }

    static function getFlash($match)
    {
        $w = $match[1];
        $h = $match[2];
        $url = $match[3];
        if (!$w)
            $w = 550;if (!$h)
            $h = 400;
        return '<embed type="application/x-shockwave-flash" src="' . $url . '" wmode="opaque" quality="high" bgcolor="#ffffff" menu="false" play="true" loop="true" width="' . $w . '" height="' . $h . '"/>';
    }

    static function getMedia($match)
    {
        $w = $match[1];
        $h = $match[2];
        $play = $match[3];
        $url = $match[4];
        if (!$w)
            $w = 550;if (!$h)
            $h = 400;
        return '<embed type="application/x-mplayer2" src="' . $url . '" enablecontextmenu="false" autostart="' . ($play == '1' ? 'true' : 'false') . '" width="' . $w . '" height="' . $h . '"/>';
    }

    static function getTable($match)
    {
        $w = $match[1];
        $b = $match[2];
        $str = '<table';
        if ($w)
            $str.=' width="' . $w . '"';
        if ($b)
            $str.=' bgcolor="' . $b . '"';
        return $str . '>';
    }

    static function getTR($match)
    {
        return '<tr' . ($match[1] ? ' bgcolor="' . $match[1] . '"' : '') . '>';
    }

    static function getTD($match)
    {
        $col = $match[1];
        $row = $match[2];
        $w = $match[3];
        return '<td' . ($col > 1 ? ' colspan="' . $col . '"' : '') . ($row > 1 ? ' rowspan="' . $row . '"' : '') . ($w ? ' width="' . $w . '"' : '') . '>';
    }

    static function getUL($match)
    {
        $str = '<ul';
        if ($match[1])
            $str.=' type="' . $match[1] . '"';
        return $str . '>';
    }

    static function ubb2html($sUBB)
    {
    //    echo $sUBB."\n";
        $sHtml = $sUBB;

        $sHtml = preg_replace("/&/", '&amp;', $sHtml);
        $sHtml = preg_replace("/</", '&lt;', $sHtml);
        $sHtml = preg_replace("/>/", '&gt;', $sHtml);
        $sHtml = preg_replace("/\t/", '&nbsp; &nbsp; &nbsp; &nbsp; ', $sHtml);
        $sHtml = preg_replace("/   /", '&nbsp; &nbsp;', $sHtml);
        $sHtml = preg_replace("/  /", '&nbsp;&nbsp;', $sHtml);
        $sHtml = preg_replace("/\r?\n/", '<br />', $sHtml);


        self::$ubb_cnum = 0;
        $sHtml = preg_replace_callback("/\[code\]([\s\S]*?)\[\/code\]/i", 'DUtil_Str::saveCodeArea', $sHtml);

        $sHtml = preg_replace("/\[(\/?)(b|u|i|s|sup|sub)\]/i", '<$1$2>', $sHtml);
        $sHtml = preg_replace("/\[color\s*=\s*([^\]]+?)\]/i", '<span style="color:$1;">', $sHtml);



        $sHtml = preg_replace_callback("/\[size\s*=\s*(\d+?)\]/i", 'DUtil_Str::getSizeName', $sHtml);
        $sHtml = preg_replace("/\[font\s*=\s*([^\]]+?)\]/i", '<span style="font-family:$1;">', $sHtml);
        $sHtml = preg_replace("/\[back\s*=\s*([^\]]+?)\]/i", '<span style="background-color:$1;">', $sHtml);
        $sHtml = preg_replace("/\[\/(color|size|font|back)\]/i", '</span>', $sHtml);

        for ($i = 0; $i < 3; $i++)
            $sHtml = preg_replace("/\[align\s*=\s*([^\]]+?)\](((?!\[align(?:\s+[^\]]+)?\])[\s\S])*?)\[\/align\]/", '<p align="$1">$2</p>', $sHtml);
        $sHtml = preg_replace("/\[img\]\s*([\s\S]+?)\s*\[\/img\]/i", '<img src="$1" />', $sHtml);

        $sHtml = preg_replace_callback("/\[img\s*=(?:\s*(\d+)\s*,\s*(\d+)\s*)?(?:,?\s*(\w+)\s*)?\]\s*([\s\S]+?)\s*\[\/img\]/i", 'DUtil_Str::getImg', $sHtml);
        $sHtml = preg_replace("/\[url\]\s*([\s\S]+?)\s*\[\/url\]/i", '<a href="$1">$1</a>', $sHtml);
        $sHtml = preg_replace("/\[url\s*=\s*([^\]\s]+?)\s*\]\s*([\s\S]+?)\s*\[\/url\]/i", '<a href="$1">$2</a>', $sHtml);
        $sHtml = preg_replace("/\[email\]\s*([\s\S]+?)\s*\[\/email\]/i", '<a href="mailto:$1">$1</a>', $sHtml);
        $sHtml = preg_replace("/\[email\s*=\s*([^\]\s]+?)\s*\]\s*([\s\S]+?)\s*\[\/email\]/i", '<a href="mailto:$1">$2</a>', $sHtml);
        $sHtml = preg_replace("/\[quote\]([\s\S]*?)\[\/quote\]/i", '<blockquote>$1</blockquote>', $sHtml);


        $sHtml = preg_replace_callback("/\[flash\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*)?\]([\s\S]+?)\[\/flash\]/i", 'DUtil_Str::getFlash', $sHtml);


        $sHtml = preg_replace_callback("/\[media\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\]([\s\S]+?)\[\/media\]/i", 'DUtil_Str::getMedia', $sHtml);


        $sHtml = preg_replace_callback("/\[table(?:\s*=\s*(\d{1,4}%?)\s*(?:,\s*([^\]]+)\s*)?)?]/i", 'DUtil_Str::getTable', $sHtml);


        $sHtml = preg_replace_callback("/\[tr(?:\s*=(\s*[^\]]+))?\]/i", 'DUtil_Str::getTR', $sHtml);
        $sHtml = preg_replace_callback("/\[td(?:\s*=\s*(\d{1,2})\s*,\s*(\d{1,2})\s*(?:,\s*(\d{1,4}%?))?)?\]/i", 'DUtil_Str::getTD', $sHtml);
        $sHtml = preg_replace("/\[\/(table|tr|td)\]/i", '</$1>', $sHtml);
        $sHtml = preg_replace("/\[\*\]([^\[]+)/i", '<li>$1</li>', $sHtml);


        $sHtml = preg_replace_callback("/\[list(?:\s*=\s*([^\]]+)\s*)?\]/i", 'DUtil_Str::getUL', $sHtml);
        $sHtml = preg_replace("/\[\/list\]/i", '</ul>', $sHtml);

        for ($i = 1; $i <= self::$ubb_cnum; $i++)
        {
            $sHtml = str_replace("[\tubbcodeplace_" . $i . "\t]", self::$ubb_arrcode[$i], $sHtml);
        }

   //     echo $sHtml."\n";
        return $sHtml;
    }

    //对中文进行二元分词的函数

    function sp_str($str)
    {

        //所有汉字后添加ASCII的0字符,此法是为了排除特殊中文拆分错误的问题

        $str = preg_replace("/[\x80-\xff]{2}/", "\\0" . chr(0x00), $str);



        //拆分的分割符

        $search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`", "^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">", "\r", "\r\n", "$", "&", "%", "#", "@", "+", "=", "{", "}", "[", "]", "：", "）", "（", "．", "。", "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、", "—", "　", "《", "》", "－", "…", "【", "】",);



        //替换所有的分割符为空格

        $str = str_replace($search, ' ', $str);



        //用正则匹配半角单个字符或者全角单个字符,存入数组$ar

        preg_match_all("/[\x80-\xff]?./", $str, $ar);
        $ar = $ar[0];



        //去掉$ar中ASCII为0字符的项目

        $acount = count($ar);

        for ($i = 0; $i < $acount; $i++)
            if ($ar[$i] != chr(0x00))
                $ar_new[] = $ar[$i];

        $ar = $ar_new;
        unset($ar_new);
        $oldsw = 0;



        $acount = count($ar);

        //把连续的半角存成一个数组下标,或者全角的每2个字符存成一个数组的下标

        for ($ar_str = '', $i = 0; $i < $acount; $i++)
        {

            $sw = strlen($ar[$i]);

            if ($i > 0 and $sw != $oldsw)
                $ar_str.=" ";

            if ($sw == 1)
                $ar_str.=$ar[$i];

            else

            if (strlen($ar[$i + 1]) == 2)
                $ar_str.=$ar[$i] . $ar[$i + 1] . ' ';

            elseif ($oldsw == 1 or $oldsw == 0)
                $ar_str.=$ar[$i];

            $oldsw = $sw;
        }



        //去掉连续的空格

        $ar_str = trim(preg_replace("# {1,}#i", " ", $ar_str));



        //返回拆分后的结果

        return explode(' ', $ar_str);
    }

    function loadF2J()
    {

        if (empty(self::$f2j))
        {

            $filename = DATA_PATH . "/f2j.txt";

            $content = file_get_contents($filename);

            $fn_list = explode("\n", $content);

            foreach ($fn_list as $fn)
            {

                $fn = trim($fn);

                if (strlen($fn) != 4)
                {

                    continue;
                }

                self::$f2j[substr($fn, 0, 2)] = substr($fn, 2, 2);
            }
        }
    }

    function loadFirstName()
    {

        if (empty(self::$firstname_single)
            && empty(self::$firstname_double))
        {

            $filename = DATA_PATH . "/keyword/firstname.txt";

            $content = file_get_contents($filename);

            $fn_list = explode("\n", $content);

            foreach ($fn_list as $fn)
            {

                $fn = trim($fn);

                if (strlen($fn) == 2)
                {

                    self::$firstname_single[$fn] = "";
                }
                else if (strlen($fn) == 4)
                {

                    self::$firstname_double[$fn] = "";
                }
            }
        }
    }

    function str2arr($srcstr)
    {

        $arr = array();

        $length = strlen($srcstr);

        for ($i = 0; $i < $length; $i++)
        {

            if (ord($srcstr[$i]) > 0x80)
            {

                $arr[] = substr($srcstr, $i, 2);

                $i++;
            }
            else
            {

                $arr[] = $srcstr[$i];
            }
        }

        return $arr;
    }

    function ft2jt($name)
    {

        DUtil_Str::loadF2J();

        $arr = DUtil_Str::str2arr($name);

        $acount = count($arr);

        for ($i = 0; $i < $acount; $i++)
        {

            if (array_key_exists($arr[$i], self::$f2j))
            {

                $arr[$i] = self::$f2j[$arr[$i]];
            }
        }

        return implode("", $arr);
    }

    function isSingleFirstName($name)
    {

        DUtil_Str::loadFirstName();

        $fn = substr($name, 0, 2);

        return array_key_exists($fn, self::$firstname_single) || array_key_exists(DUtil_Str::ft2jt($fn), self::$firstname_single);
    }

    function isDoubleFirstName($name)
    {

        DUtil_Str::loadFirstName();

        $fn = substr($name, 0, 4);

        return array_key_exists($fn, self::$firstname_double) || array_key_exists(DUtil_Str::ft2jt($fn), self::$firstname_double);
    }

    function isRealName($name)
    {

        if (strlen($name) == 4)
        {

            if (strlen(DUtil_Str::subString($name, 1)) == 0
                && strlen(DUtil_Str::subString($name, 3)) == 2)
            {

                return!DUtil_Str::isInvalidName($name) && DUtil_Str::isSingleFirstName($name);
            }
        }
        else if (strlen($name) == 6)
        {

            if (strlen(DUtil_Str::subString($name, 1)) == 0
                && strlen(DUtil_Str::subString($name, 3)) == 2
                && strlen(DUtil_Str::subString($name, 5)) == 4)
            {

                return!DUtil_Str::isInvalidName($name) && DUtil_Str::isSingleFirstName($name) || DUtil_Str::isDoubleFirstName($name);
            }
        }
        else if (strlen($name) == 8)
        {

            if (strlen(DUtil_Str::subString($name, 1)) == 0
                && strlen(DUtil_Str::subString($name, 3)) == 2
                && strlen(DUtil_Str::subString($name, 5)) == 4
                && strlen(DUtil_Str::subString($name, 7)) == 6)
            {

                return!DUtil_Str::isInvalidName($name) && DUtil_Str::isDoubleFirstName($name);
            }
        }

        return false;
    }

    function format2image($format)
    {

        $imagedata = array(
            "doc" => "http://" . IMG_HOST . "/i/suffix/doc.gif",
            "docx" => "http://" . IMG_HOST . "/i/suffix/doc.gif",
            "xls" => "http://" . IMG_HOST . "/i/suffix/xls.gif",
            "xlsx" => "http://" . IMG_HOST . "/i/suffix/xls.gif",
            "txt" => "http://" . IMG_HOST . "/i/suffix/txt.gif",
            "exe" => "http://" . IMG_HOST . "/i/suffix/exe.gif",
            "com" => "http://" . IMG_HOST . "/i/suffix/exe.gif",
            "bat" => "http://" . IMG_HOST . "/i/suffix/exe.gif",
            "swf" => "http://" . IMG_HOST . "/i/suffix/swf.gif",
            "html" => "http://" . IMG_HOST . "/i/suffix/html.gif",
            "htm" => "http://" . IMG_HOST . "/i/suffix/html.gif",
            "pdf" => "http://" . IMG_HOST . "/i/suffix/pdf.gif",
            "ppt" => "http://" . IMG_HOST . "/i/suffix/ppt.gif",
            "pptx" => "http://" . IMG_HOST . "/i/suffix/ppt.gif",
            "pps" => "http://" . IMG_HOST . "/i/suffix/ppt.gif",
            "ppsx" => "http://" . IMG_HOST . "/i/suffix/ppt.gif",
            "jpg" => "http://" . IMG_HOST . "/i/suffix/jpg.gif",
            "jpeg" => "http://" . IMG_HOST . "/i/suffix/jpg.gif",
            "pjpeg" => "http://" . IMG_HOST . "/i/suffix/jpg.gif",
            "gif" => "http://" . IMG_HOST . "/i/suffix/gif.gif",
            "png" => "http://" . IMG_HOST . "/i/suffix/png.gif",
            "bmp" => "http://" . IMG_HOST . "/i/suffix/bmp.gif",
            "psd" => "http://" . IMG_HOST . "/i/suffix/psd.gif",
            "zip" => "http://" . IMG_HOST . "/i/suffix/zip.gif",
            "7z" => "http://" . IMG_HOST . "/i/suffix/zip.gif",
            "gz" => "http://" . IMG_HOST . "/i/suffix/zip.gif",
            "tgz" => "http://" . IMG_HOST . "/i/suffix/zip.gif",
            "rar" => "http://" . IMG_HOST . "/i/suffix/zip.gif",
            "msi" => "http://" . IMG_HOST . "/i/suffix/msi.gif",
            "mp3" => "http://" . IMG_HOST . "/i/suffix/mp3.gif",
            "wma" => "http://" . IMG_HOST . "/i/suffix/mp3.gif",
            "wav" => "http://" . IMG_HOST . "/i/suffix/mp3.gif",
            "rm" => "http://" . IMG_HOST . "/i/suffix/rm.gif",
            "mpg" => "http://" . IMG_HOST . "/i/suffix/mpg.gif",
            "mpeg" => "http://" . IMG_HOST . "/i/suffix/mpg.gif",
            "mpe" => "http://" . IMG_HOST . "/i/suffix/mpg.gif",
            "wmv" => "http://" . IMG_HOST . "/i/suffix/mpg.gif",
            "mov" => "http://" . IMG_HOST . "/i/suffix/mpg.gif",
            "chm" => "http://" . IMG_HOST . "/i/suffix/chm.gif",
        );



        $format = strtolower($format);

        if (array_key_exists($format, $imagedata))
        {

            return $imagedata[$format];
        }

        return "http://" . IMG_HOST . "/i/suffix/ot.gif";
    }

    function size2str($size)
    {

        if ($size < 1024)
        {

            return $size . "字节";
        }
        else if ($size < 1024 * 1024)
        {

            return round($size / 1024, 1) . "K";
        }
        else if ($size < 1024 * 1024 * 1024)
        {

            return round($size / (1024 * 1024), 2) . "M";
        }
        else
        {

            return round($size / (1024 * 1024 * 1024), 2) . "G";
        }
    }

    function msize2str($size)
    {

        if ($size < 1024)
        {

            return $size . "M";
        }
        else if ($size < 1024 * 1024)
        {

            return round($size / 1024, 2) . "G";
        }
    }

    function size2intstr($size)
    {

        if ($size < 1024)
        {

            return $size . "字节";
        }
        else if ($size < 1024 * 1024)
        {

            return round($size / 1024, 0) . "K";
        }
        else if ($size < 1024 * 1024 * 1024)
        {

            return round($size / (1024 * 1024), 0) . "M";
        }
        else
        {

            return round($size / (1024 * 1024 * 1024), 0) . "G";
        }
    }

    function getipinfo($ip)
    {

        $arr = explode(".", $ip);

        $arr[count($arr) - 1] = "*";

        return 'IP：' . implode(".", $arr);
    }

    function hz2py($str, &$firststr_arr, &$pystr_arr, &$filterstr="", $src_id = "hz2py")
    {

        $firststr_arr = array("");

        $pystr_arr = array("");

        $filterstr = "";

        $singleflag = 0;



        $id = dba_open(DATA_PATH . "/py/" . "/" . $src_id, "r", "gdbm");

        if (!$id)
        {

            return;
        }

        $len = strlen($str);

        for ($i = 0; $i < $len; $i++)
        {

            $p = ord(substr($str, $i, 1));

            if ($p > 128)
            {

                $p = substr($str, $i, 2);

                $i++;

                $pystrtemp = dba_fetch($p, $id);

                $arr = split(",", $pystrtemp);



                //拼音

                $pystr_arr2 = array();

                foreach ($pystr_arr as $py)
                {

                    foreach ($arr as $value)
                    {

                        $pystr_arr2[] = $py . strtolower($value);
                    }
                }

                $pystr_arr = $pystr_arr2;



                //拼音头字母

                $firststr_arr2 = array();

                foreach ($firststr_arr as $firstpy)
                {

                    foreach ($arr as $value)
                    {

                        $firststr_arr2[] = $firstpy . substr(strtolower($value), 0, 1);
                    }
                }

                $firststr_arr = $firststr_arr2;



                //过滤字符串

                if (0 != strlen($pystrtemp))
                {

                    $filterstr .= $p;
                }
            }
            else
            {

                //$singleflag = 1;

                $char = strtolower(substr($str, $i, 1));

                if (ord($char) >= 97 && ord($char) <= 122)
                {

                    //拼音

                    foreach ($pystr_arr as $k => $py)
                    {

                        $pystr_arr[$k] .= strtolower(substr($str, $i, 1));
                    }

                    //拼音头字母

                    foreach ($firststr_arr as $k => $firstpy)
                    {

                        $firststr_arr[$k] .= strtolower(substr($str, $i, 1));
                    }

                    //过滤字符串

                    $filterstr .= substr($str, $i, 1);
                }
            }
        }

        dba_close($id);



        if ($singleflag == 1)
        {

            $firststr_arr = array();
        }

        return true;
    }

    //搜索输入 是否匹配拼音

    public static function isPinyinMatch($hanzi, $search)
    {

        if (stripos($hanzi, $search) === 0)
            return true;



        DUtil_Str::hz2py($hanzi, $firststr_arr, $pystr_arr);



        $py_arr = array_merge($firststr_arr, $pystr_arr);



        foreach ($py_arr as $v)
        {

            if (stripos($v, $search) === 0)
            {

                return true;
            }
        }



        return false;
    }

    function getRecentCareer($list)
    {

        $ret = "";

        $ym = "000000";

        foreach ($list as $item)
        {

            $year = $item["beginyear"];

            if ($year == "")
            {

                $year = "0000";
            }

            $month = $item["beginmonth"];

            if ($month == "")
            {

                $month = "00";
            }



            if ($ym <= $year . $month)
            {

                $ret = $item["company"];

                $ym = $year . $month;
            }
        }

        return $ret;
    }

    function getRecentEducation($list)
    {

        $ret = "";

        $y = "0000";

        foreach ($list as $item)
        {

            $year = $item["year"];

            if ($year == "")
            {

                $year = "0000";
            }



            if ($ym <= $year)
            {

                $ret = $item["school"];

                $ym = $year;
            }
        }

        return $ret;
    }

    function getCareer($v)
    {

        $time = "";

        if ($v["endyear"] == "" && $v["endmonth"] == "")
        {

            $time = "至今";
        }

        if ($v["endmonth"] != "")
        {

            $time = $v["endmonth"] . "月" . $time;
        }

        if ($v["endyear"] != "")
        {

            $time = $v["endyear"] . "年" . $time;
        }

        $time = " - " . $time;

        if ($v["beginmonth"] != "")
        {

            $time = $v["beginmonth"] . "月" . $time;
        }

        if ($v["beginyear"] != "")
        {

            $time = $v["beginyear"] . "年" . $time;
        }



        return $v["company"] . " " . $v["dept"] . " " . $time;
    }

    function getEducation($v)
    {

        $year = "";

        if ($v["year"] != "")
        {

            $year = $v["year"] . "年入学";
        }

        return $v["school"] . " " . $v["class"] . " " . $year;
    }

    function imageFormat($format)
    {

        if ($format != "gif" && $format != "png")
        {

            $format = "jpg";
        }

        return $format;
    }

    function timeformat($t, $needyestime=false)
    {

        $nowtime = time();

        $time = strtotime($t);

        $md = date("m-d", $time);



        if (date("Y", $time) != date("Y", $nowtime))
        {

            return date("m月d日", $time);
        }
        else if ($md != date("m-d", $nowtime))
        {

            if ($needyestime)
            {

                if ($md == date("m-d", ($nowtime - 86400)))
                {

                    return date("昨天H:i", $time);
                }
                else if ($md == date("m-d", ($nowtime - 2 * 86400)))
                {

                    return date("前天H:i", $time);
                }
                else
                {

                    return date("m月d日", $time);
                }
            }
            else
            {

                return date("m月d日", $time);
            }
        }
        else
        {

            return date("H:i", $time);
        }
    }

    function getBirthText($birthday, $lunarbirth, $hidebirthyear=0)
    {

        global $ga_agedata;

        list($year, $month, $day) = explode("-", $birthday);

        if ($year < 1900)
        {

            return $ga_agedata[$year] . " " . $month . "月" . $day . "日";
        }

        if ($lunarbirth)
        {

            $clunar = new CLunar(intval($year), intval($month), intval($day));

            $year = $clunar->cyclical($clunar->yearCyl);

            $month = ($clunar->isLeap ? "闰" : "") . $clunar->cMon($clunar->month);

            $day = $clunar->cDay($clunar->day);
        }

        if ($hidebirthyear)
        {

            // 对外隐藏出生年份

            return $month . "月" . $day . "日";
        }
        else
        {

            return $year . "年" . $month . "月" . $day . "日";
        }
    }

    function getAge($birthday)
    {

        global $ga_agedata;

        $year = intval(substr($birthday, 0, 4));

        if ($year < 1900)
        {

            return $ga_agedata[$year];
        }

        return (intval(date("Y")) - $year) . "岁";
    }

    static function ts2s($tt)
    {

        $now = time();
        $interval = $now - $tt;

        $t = date("Y-m-d H:i:s", $tt);
        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        list($nyear, $nmonth, $nday, $nh, $nm, $ns) = sscanf(date("Y-m-d H:i:s"), "%d-%d-%d %d:%d:%d");



        if ($interval < 0)
        {

            return "未来";
        }
        else if ($interval < 60)
        {

            return $interval . "秒前";
        }
        else if ($interval < 3600)
        {

            return round($interval / 60) . "分钟前";
        }
        else if ($interval < 86400)
        {

            return round($interval / 3600) . "小时前";
        }
        else if ($interval < 86400 * 3)
        {

            return round($interval / 86400) . "天前";
        }
        else if ($year == $nyear)
        {

            return $month . "月" . $day . "日";
        }

        return $year . "年" . $month . "月" . $day . "日";
    }

    //weiqun
    static function weiqunts2s($tt)
    {
        $now = time();
        $interval = $now - $tt;
        $t = date("Y-m-d H:i:s", $tt);
        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");
        list($nyear, $nmonth, $nday, $nh, $nm, $ns) = sscanf(date("Y-m-d H:i:s"), "%d-%d-%d %d:%d:%d");

        if ($interval < 0)
        {
            return "未来";
        }
        else if ($interval < 60)
        {
            return $interval . "秒前";
        }
        else if ($interval < 3600)
        {
            return round($interval / 60) . "分钟前";
        }
        else if ($interval < 86400 && $day == $nday)
        {
            return '今天 '. $h . ":" .$m;
        }
        else if ($year == $nyear)
        {

            return $month . "月" . $day . "日 ". $h . ":" .$m;
        }

        return $year . "年" . $month . "月" . $day . "日 ". $h . ":" .$m;
    }

    static function t2s($t)
    {
        $tt = strtotime($t);
        return self::ts2s($tt);
    }

    function t2s2($t)
    {

        $now = time();

        $tt = strtotime($t);

        $interval = $tt - $now;



        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        list($nyear, $nmonth, $nday, $nh, $nm, $ns) = sscanf(date("Y-m-d H:i:s"), "%d-%d-%d %d:%d:%d");



        if ($interval < 0)
        {

            return "";
        }
        else if ($interval < 60)
        {

            return $interval . "秒后";
        }
        else if ($interval < 3600)
        {

            return round($interval / 60) . "分钟后";
        }
        else if ($interval < 86400)
        {

            return round($interval / 3600) . "小时后";
        }
        else if ($interval < 86400 * 3)
        {

            return round($interval / 86400) . "天后";
        }
        else if ($year == $nyear)
        {

            return $month . "月" . $day . "日";
        }

        return $year . "年" . $month . "月" . $day . "日";
    }

    function t2s3($t)
    {

        $now = time();

        $tt = strtotime($t);

        $interval = $now - $tt;



        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        list($nyear, $nmonth, $nday, $nh, $nm, $ns) = sscanf(date("Y-m-d H:i:s"), "%d-%d-%d %d:%d:%d");



        if ($interval < 0)
        {

            return "未来";
        }
        else if ($interval < 60)
        {

            return $interval . "秒前";
        }
        else if ($interval < 3600)
        {

            return round($interval / 60) . "分钟前";
        }
        else if ($interval < 86400)
        {

            return round($interval / 3600) . "小时前";
        }

        return date("Y-m-d H:i", $tt);
    }

    function t2s4($t)
    {

        $now = time();

        $tt = strtotime($t);

        $interval = $now - $tt;



        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        list($nyear, $nmonth, $nday, $nh, $nm, $ns) = sscanf(date("Y-m-d H:i:s"), "%d-%d-%d %d:%d:%d");



        if ($month < 10)
            $month = "0" . $month;

        if ($day < 10)
            $day = "0" . $day;

        if ($h < 10)
            $h = "0" . $h;

        if ($m < 10)
            $m = "0" . $m;



        if ($interval < 0)
        {

            return "未来";
        }
        else if ($interval < 60)
        {

            return $interval . "秒前";
        }
        else if ($interval < 3600)
        {

            return round($interval / 60) . "分钟前";
        }
        else if ($interval < 86400)
        {

            return round($interval / 3600) . "小时前";
        }
        else if ($year == $nyear)
        {

            return $month . "月" . $day . "日 " . $h . ":" . $m;
        }

        return $year . "年" . $month . "月" . $day . "日 " . $h . ":" . $m;
    }

    function t2s5($t)
    {

        $t_today = strtotime(date("Y-m-d 23:59:59"));

        $tt = strtotime($t);

        $interval = $t_today - $tt;



        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        if ($month < 10)
            $month = "0" . $month;

        if ($day < 10)
            $day = "0" . $day;

        if ($h < 10)
            $h = "0" . $h;

        if ($m < 10)
            $m = "0" . $m;



        if ($interval >= 0)
        {

            if ($interval < 86400)
            {

                return "今天 " . $h . ":" . $m;
            }
            else if ($interval < 86400 * 2)
            {

                return "昨天 " . $h . ":" . $m;
            }
        }

        if ($year == date("Y"))
        {

            return $month . "月" . $day . "日 " . $h . ":" . $m;
        }



        return $year . "年" . $month . "月" . $day . "日 " . $h . ":" . $m;
    }

    /*

      0-60秒内：   刚刚

      1-59分钟：   N分钟前

      今天 HH:MM

      昨天 HH:MM

      前天 HH:MM

      当年：       M月D日 HH:MM

      跨年：       YYYY年M月D日 HH:MM

     */

    function t2s6($t)
    {

        $now = time();

        $tt = strtotime($t);

        $interval = $now - $tt;



        if ($interval <= 60)
        {

            return "刚刚";
        }



        if ($interval < 3600)
        {

            return round($interval / 60) . "分钟前";
        }



        list($year, $month, $day, $h, $m, $s) = sscanf($t, "%d-%d-%d %d:%d:%d");

        if ($month < 10)
            $month = "0" . $month;

        if ($day < 10)
            $day = "0" . $day;

        if ($h < 10)
            $h = "0" . $h;

        if ($m < 10)
            $m = "0" . $m;



        $t_today = strtotime(date("Y-m-d 23:59:59"));

        $interval2 = $t_today - $tt;

        if ($interval2 >= 0)
        {

            if ($interval2 < 86400)
            {

                return "今天 " . $h . ":" . $m;
            }
            else if ($interval2 < 86400 * 2)
            {

                return "昨天 " . $h . ":" . $m;
            }
            else if ($interval2 < 86400 * 3)
            {

                return "前天 " . $h . ":" . $m;
            }
        }



        if ($year == date("Y", $now))
        {

            return $month . "月" . $day . "日 " . $h . ":" . $m;
        }
        else
        {

            return $year . "年" . $month . "月" . $day . "日 " . $h . ":" . $m;
        }
    }

    function s2hm($interval)
    {

        $hour = floor($interval / 3600);

        $minute = floor(($interval - $hour * 3600) / 60);

        if ($hour && $minute)
        {

            return $hour . "小时" . $minute . "分";
        }
        else if ($minute)
        {

            return $minute . "分";
        }
        else if ($hour)
        {

            return $hour . "小时整";
        }
        else
        {

            return $interval . "秒";
        }
    }

    // GB18030 有单字节、双字节和四字节三种方式。
    // 单字节 -- 0x00-0x7F
    // 双字节 -- 高字节 0x81-0xFE 低字节 0x40-0x7E 0x80-0xFE
    // 四字节 -- 第一、三字节 0x81-0xFE，第二、四字节 0x30-0x39

    static function subString($srcstr, $length, $ext = '')
    {
        $srcstr = strval($srcstr);
        if (strlen($srcstr) > $length)
        {

            $ext = trim($ext);

            if (strlen($ext) > 0)
            {
                $length = $length - strlen($ext);
            }

            $lastlen = 0;

            for ($i = 0; $i <= $length; $i++)
            {
                $lastlen = $i;
                if (ord($srcstr[$i]) > 0x80)
                {
                    $i++;
                    if (ord($srcstr[$i]) < 0x40)
                    {
                        $i += 2;
                    }
                }
            }

            $srcstr = substr($srcstr, 0, $lastlen) . $ext;
        }

        return $srcstr;
    }

    function lenString($srcstr)
    {
        mb_internal_encoding(SYS_CHARSET);
        return mb_strwidth($srcstr);
    }

    // 解决 錦 躙 等字 用php的addslashes之后多一个\的问题

    function addslashes($text)
    {

        for (;;)
        {

            $i = mb_strpos($text, chr(92), 0, "GBK");

            if ($i === false)
            {

                break;
            }

            $T = mb_substr($text, 0, $i, "GBK") . chr(92) . chr(92);

            $text = substr($text, strlen($T) - 1);

            $OK .= $T;
        }

        $text = $OK . $text;

        $text = str_replace(chr(39), chr(92) . chr(39), $text);

        $text = str_replace(chr(34), chr(92) . chr(34), $text);

        return $text;
    }

    function a2s($arr)
    {

        $str = "";

        foreach ($arr as $key => $value)
        {

            if (is_array($value))
            {

                foreach ($value as $value2)
                {

                    $str .= urlencode($key) . "[]=" . urlencode($value2) . "&";
                }
            }
            else
            {

                $str .= urlencode($key) . "=" . urlencode($value) . "&";
            }
        }

        return $str;
    }

    function s2a($str)
    {

        $arr = array();

        parse_str($str, $arr);

        return $arr;
    }

    function aa2s($arr)
    {

        $str = "";

        foreach ($arr as $value)
        {

            $str .= DUtil_Str::a2s($value) . "\n";
        }

        return $str;
    }

    function s2aa($str)
    {

        $arr = array();

        $lines = explode("\n", $str);

        foreach ($lines as $line)
        {

            if (0 == strlen($line))
            {

                continue;
            }
            else
            {

                $arr[] = DUtil_Str::s2a($line);
            }
        }

        return $arr;
    }

    function getAstro($month, $day)
    {

        if (($month == 12 && $day >= 22)
            || ($month == 1 && $day <= 20))
        {

            return "摩羯座";
        }
        else if (($month == 1 && $day >= 21)
            || ($month == 2 && $day <= 19))
        {

            return "水瓶座";
        }
        else if (($month == 2 && $day >= 20)
            || ($month == 3 && $day <= 20))
        {

            return "双鱼座";
        }
        else if (($month == 3 && $day >= 21)
            || ($month == 4 && $day <= 20))
        {

            return "白羊座";
        }
        else if (($month == 4 && $day >= 21)
            || ($month == 5 && $day <= 21))
        {

            return "金牛座";
        }
        else if (($month == 5 && $day >= 22)
            || ($month == 6 && $day <= 21))
        {

            return "双子座";
        }
        else if (($month == 6 && $day >= 22)
            || ($month == 7 && $day <= 22))
        {

            return "巨蟹座";
        }
        else if (($month == 7 && $day >= 23)
            || ($month == 8 && $day <= 23))
        {

            return "狮子座";
        }
        else if (($month == 8 && $day >= 24)
            || ($month == 9 && $day <= 23))
        {

            return "处女座";
        }
        else if (($month == 9 && $day >= 24)
            || ($month == 10 && $day <= 23))
        {

            return "天秤座";
        }
        else if (($month == 10 && $day >= 24)
            || ($month == 11 && $day <= 22))
        {

            return "天蝎座";
        }
        else if (($month == 11 && $day >= 23)
            || ($month == 12 && $day <= 21))
        {

            return "射手座";
        }

        return "";
    }

    static function space2nbsp($str)
    {

        return str_replace("\n ", "\n&nbsp;", str_replace("  ", "&nbsp; ", $str));
    }

    function IsImage($url)
    {

        $imgfile = array(".gif", ".png", ".x-png", ".jpg", ".jpeg", "pjpeg");



        foreach ($imgfile as $tmp)
        {

            $len = strlen($tmp);

            if (0 == strncasecmp($tmp, substr($url, strlen($url) - $len), $len))
            {

                return true;
            }
        }

        return false;
    }

    /*

     * 截断带链接的字符串

     * ！！！注意：只能处理仅含A标签的文字

     * 此函数有bug，推荐使用 CHtmlParse::getAbstract() jianqing 2010.7.27

     */

    function subStringWithLink($s, $len, $ext='')
    {

        list($p1, $p2) = CHtmlParse::getAbstract($s, $len, $ext);

        return $p1;
    }

    static function AddLink($src, $withimg)
    {


        if (0 == preg_match("/mms:\/\/|http:\/\/|ftp:\/\/|https:\/\/|www\./i", $src, $res, PREG_OFFSET_CAPTURE))
        {

            return DUtil_Str::space2nbsp($src);
        }

        $len = strlen($src);

        $start = $res[0][1];

        for ($end = $start; $end < $len; $end++)
        {

            $vchr = $src[$end];

            $ov = ord($vchr);



            if ($end + 6 < $len)
            {

                $fourchar = substr($src, $end, 4);

                $sixchar = substr($src, $end, 6);

                if ($fourchar == "&lt;" || $fourchar == "&gt;")
                {

                    break;
                }

                if ($sixchar == "&quot;")
                {

                    break;
                }
            }
            else if ($end + 4 < $len)
            {

                $fourchar = substr($src, $end, 4);

                if ($fourchar == "&lt;" || $fourchar == "&gt;")
                {

                    break;
                }
            }

            if ($ov <= 32
                || $vchr == "'"
                || $vchr == '"'
                || $vchr == '<'
                || $vchr == '>'
                || $ov >= 128)
            {

                break;
            }
        }

        $url = substr($src, $start, $end - $start);

        $posgt = strpos($src, ">", $end);

        $poslt = strpos($src, "<", $end);

        if (($posgt !== false && $poslt !== false && $poslt > $posgt)
            || ($posgt !== false && $poslt === false))
        {

            return DUtil_Str::space2nbsp(substr($src, 0, $start)) . $url . DUtil_Str::AddLink(substr($src, $end), $withimg);
        }
        else if ($withimg && DUtil_Str::IsImage($url))
        {

            return DUtil_Str::space2nbsp(substr($src, 0, $start)) . "<img src=\"" . (strtolower(substr($url, 0, 4)) == "www." ? "http://" . $url : $url) . "\" border=0>" . DUtil_Str::AddLink(substr($src, $end), $withimg);
        }

        // 去除url尾部的空格

        $nbsp = "";

        while (substr($url, -6, 6) == "&nbsp;")
        {

            $nbsp .= "&nbsp;";

            $url = substr($url, 0, strlen($url) - 6);
        }



        return DUtil_Str::space2nbsp(substr($src, 0, $start)) . "<a href=\"" . (strtolower(substr($url, 0, 4)) == "www." ? "http://" . $url : $url) . "\" target=_blank >" . $url . "</a>" . DUtil_Str::AddLink($nbsp . substr($src, $end), $withimg);
    }

    function AddSafeLink($src, $withimg)
    {

        if (0 == preg_match("/mms:\/\/|http:\/\/|ftp:\/\/|https:\/\/|www\./i", $src, $res, PREG_OFFSET_CAPTURE))
        {

            return DUtil_Str::space2nbsp($src);
        }

        $len = strlen($src);

        $start = $res[0][1];

        for ($end = $start; $end < $len; $end++)
        {

            if (ord($src[$end]) <= 32
                || $src[$end] == "'"
                || $src[$end] == '"'
                || $src[$end] == '<'
                || $src[$end] == '>'
                || ord($src[$end]) >= 128)
            {

                break;
            }
        }

        $url = substr($src, $start, $end - $start);

        $posgt = strpos($src, ">", $end);

        $poslt = strpos($src, "<", $end);

        if (($posgt !== false && $poslt !== false && $poslt > $posgt)
            || ($posgt !== false && $poslt === false))
        {

            return DUtil_Str::space2nbsp(substr($src, 0, $start)) . $url . DUtil_Str::AddSafeLink(substr($src, $end), $withimg);
        }
        else if ($withimg && DUtil_Str::IsImage($url))
        {

            return DUtil_Str::space2nbsp(substr($src, 0, $start)) . "<img src=\"" . (strtolower(substr($url, 0, 4)) == "www." ? "http://" . $url : $url) . "\" border=0>" . DUtil_Str::AddSafeLink(substr($src, $end), $withimg);
        }

        // 去除url尾部的空格

        $nbsp = "";

        while (substr($url, -6, 6) == "&nbsp;")
        {

            $nbsp .= "&nbsp;";

            $url = substr($url, 0, strlen($url) - 6);
        }

        return DUtil_Str::space2nbsp(substr($src, 0, $start)) . "<a href=\"###\" onclick=\"javascript:safeLinkAlert('" . (strtolower(substr($url, 0, 4)) == "www." ? "http://" . $url : $url) . "'); event.cancelBubble=true;\" onmousedown=\"javascript:event.cancelBubble=true;\">" . $url . "</a>" . DUtil_Str::AddSafeLink($nbsp . substr($src, $end), $withimg);
    }

    function html2abstract($content, $length, $ext = '')
    {

        $content = str_replace(array("\r", "\n"), array(" ", " "), $content);

        $content = preg_replace("/<.*?>/", " ", $content);

        $content = str_replace(array("&quot;", "&#039;", "&lt;", "&gt;", "&nbsp;"), array("\"", "'", "<", ">", " "), $content);

        $content = str_replace("&amp;", "&", $content);



        return DUtil_Str::subString($content, $length, $ext);
    }

    function html2keyabstract($content, $keyword, $prelen, $totallen)
    {

        $content = str_replace(array("\r", "\n"), array(" ", " "), $content);

        $content = preg_replace("/<.*?>/", " ", $content);

        $content = str_replace(array("&quot;", "&#039;", "&lt;", "&gt;", "&nbsp;"), array("\"", "'", "<", ">", " "), $content);

        $content = str_replace("&amp;", "&", $content);



        if (($pos = strpos($content, $keyword)) !== false)
        {

            $begin = $pos - $prelen;

            for ($i = 0; $i < $begin; $i++)
            {

                if (ord($content[$i]) > 0x80)
                {

                    $i++;
                }
            }

            if ($i)
            {

                $content = "..." . substr($content, $i);
            }
        }

        $len = strlen($content);

        if ($len > $totallen + 4)
        {

            return DUtil_Str::subString($content, $totallen, "...");
        }
        else
        {

            return $content;
        }
    }

    function html2abstract_keepstate($str, $length, $ext = '')
    {

        preg_match_all("/<img.*?>/", $str, $ret);



        $facedata = array();

        $repldata = array();

        $no = 0;

        if (is_array($ret[0]))
        {

            foreach ($ret[0] as $v)
            {

                $item = trim($v, "<>");

                $items = explode(" ", $item);

                $data = array();

                foreach ($items as $v2)
                {

                    list($name, $value) = explode("=", $v2, 2);

                    $name = strtolower($name);

                    $data[$name] = trim($value, "\"'");
                }

                list($domain, $filename) = explode("/i/state/", $data["src"], 2);

                $domain = strtolower($domain);

                if ($domain == "http://" . IMG_HOST)
                {

                    list($_no, $suffix) = explode(".", $filename, 2);

                    if ($suffix == "gif")
                    {

                        $no++;

                        $facedata[] = $v;

                        $repldata[] = "FACE" . sprintf("%02d", $no);
                    }
                }
            }

            if ($facedata)
            {

                $str = str_replace($facedata, $repldata, $str);
            }
        }



        $str = DUtil_Str::html2abstract($str, $length, $ext);

        $str = htmlspecialchars($str);

        $str = str_replace($repldata, $facedata, $str);



        $extlen = strlen($ext);

        for ($i = 1; $i <= 5; $i++)
        {

            $tail = substr($str, 0 - $extlen - $i, $i);

            if (0 == strncmp($tail, "FACE", min($i, 4)))
            {

                $str = substr($str, 0, 0 - $extlen - $i) . $ext;

                break;
            }
        }



        return $str;
    }

    function html2abstract_keepface($str, $length, $ext = '', $replacewithtext = false)
    {

        preg_match_all("/<img.*?>/", $str, $ret);



        $facedata = array();

        $repldata = array();
        $nonewface = 0;

        if (is_array($ret[0]))
        {

            foreach ($ret[0] as $v)
            {

                $item = trim($v, "<>");

                $items = explode(" ", $item);

                $data = array();

                foreach ($items as $v2)
                {

                    list($name, $value) = explode("=", $v2, 2);

                    $name = strtolower($name);

                    $data[$name] = trim($value, "\"'");
                }
                // 新版表情替换
                $pos = strpos($data["src"], "/i/state/");
                if (false !== strpos($data["src"], "/i/state/"))
                {
                    list($domain, $filename) = explode("/i/state/", $data["src"], 2);
                    $domain = substr($domain, 7); // 去除http://
                    if (CKxApp::isImgHost($domain))
                    {
                        list($_no, $suffix) = explode(".", $filename, 2);
                        if ($suffix == "gif")
                        {
                            $nonewface++;
                            $facedata[] = $v;
                            $repldata[] = "FACE" . sprintf("%03d", $nonewface);
                        }
                    }
                }
                else // 老表情仍保留
                {
                    list($domain, $filename) = explode("/i/face/", $data["src"], 2);

                    $domain = substr($domain, 7); // 去除http://

                    if (CKxApp::isImgHost($domain))
                    {

                        list($no, $suffix) = explode(".", $filename, 2);

                        if ($suffix == "gif" && is_numeric($no))
                        {

                            $facedata[] = $v;

                            $repldata[] = "FAC" . sprintf("%03d", $no);
                        }
                    }
                }
            }

            if ($facedata)
            {

                $str = str_replace($facedata, $repldata, $str);
            }
        }



        $str = DUtil_Str::html2abstract($str, $length, $ext);

        $str = htmlspecialchars($str);

        if (!$replacewithtext)
        {

            $str = str_replace($repldata, $facedata, $str);
        }



        $extlen = strlen($ext);

        if ($extlen && substr($str, 0 - $extlen) != $ext)
        {

            $extlen = 0;
        }

        for ($i = 1; $i <= 5; $i++)
        {

            $tail = substr($str, 0 - $extlen - $i, $i);

            if (0 == strncmp($tail, "FAC", min($i, 3)))
            {

                $str = substr($str, 0, 0 - $extlen - $i) . ($extlen ? $ext : "");

                break;
            }
            if (0 == strncmp($tail, "FACE", min($i, 4)))
            {
                $str = substr($str, 0, 0 - $extlen - $i) . ($extlen ? $ext : "");
                break;
            }
        }



        return $str;
    }

    //检查一个字符串是否含有url（改进了：Modify by XuXinhua 2011-03-11）

    function hasLink($str)
    {

        if (ereg("(mms://|http://|ftp://|https://|www\.)[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)+[^\s.]*", $str))
        {

            return true;
        }

        return false;
    }

    function extractLinks($src, &$urls)
    {

        if (0 == preg_match("#(mms://|http://|ftp://|https://|www\.)[_a-zA-Z0-9-]+(\.[_\?=a-zA-Z0-9-]+)+[^\s.]*#i", $src, $res, PREG_OFFSET_CAPTURE))
        {

            return DUtil_Str::space2nbsp($src);
        }

        $len = strlen($src);

        $start = $res[0][1];

        for ($end = $start; $end < $len; $end++)
        {

            if (ord($src[$end]) <= 0x20
                || $src[$end] == "'"
                || $src[$end] == '"'
                || $src[$end] == '<'
                || $src[$end] == '>'
                || ord($src[$end]) >= 0x80)
            {

                break;
            }
        }

        $urls[] = substr($src, $start, $end - $start);

        DUtil_Str::extractLinks(substr($src, $end), $urls);
    }

    function full2semi($content)
    {

        $fullchars = "０１２３４５６７８９ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺ（）〔〕【】〖〗“”‘’｛｝《》％＋—－～：。、，；？！…‖｜〃　";

        $semichars = "01234567891234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ()[][][][\"``{}<>%+---:...,?!-||\"";

        $len = strlen($content);

        $retstr = "";

        for ($i = 0; $i < $len; $i++)
        {

            if (ord($content[$i]) > 0x80)
            {

                $char = $content[$i] . $content[$i + 1];

                $i++;
            }
            else
            {

                $char = $content[$i];
            }



            if (strlen($char) == 1)
            {

                $retstr.=$char;
            }
            else if (strlen($char) == 2)
            {

                $pos = strpos($fullchars, $char);

                if (($pos !== false) && (($pos % 2) == 0))
                {

                    $retstr.=$semichars[$pos / 2];

                    $count++;
                }
                else
                {

                    $retstr.=$char;
                }
            }
        }

        return $retstr;
    }

    function extractEnNumbers($content)
    {

        //全转化为半角

        $content = DUtil_Str::full2semi($content);

        $ennumbers = "1234567890";

        $len = strlen($content);

        $num = "";

        for ($i = 0; $i < $len; $i++)
        {

            if (ord($content[$i]) > 0x80)
            {

                $char = $content[$i] . $content[$i + 1];

                $i++;
            }
            else
            {

                $char = $content[$i];
            }

            if (strlen($char) == 1 && (strpos($ennumbers, $char) !== false))
            {

                $num .= $char;
            }
        }

        return $num;
    }

    function extractNumbers($content)
    {

        $len = strlen($content);

        $zhnumbers = "一二三四五六七八九〇零壹贰叁肆伍陆柒捌玖１２３４５６７８９０ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ";

        $zh_ennumbers = "1234567890012345678912345678901234567890";

        $ennumbers = "1234567890";

        $char = "";

        $numstr = "";

        $continue = false;

        $lastchar = "";

        $lastpos = -1;

        $fullstr = "";

        $count = 0;

        for ($i = 0; $i < $len; $i++)
        {

            if (ord($content[$i]) > 0x80)
            {

                $char = $content[$i] . $content[$i + 1];

                $i++;
            }
            else
            {

                $char = $content[$i];
            }



            if (strlen($char) == 1 && (strpos($ennumbers, $char) !== false))
            {

                if ($continue)
                {

                    $numstr .=$lastchar;

                    $count++;
                }

                $lastchar = $char;

                $continue = true;
            }
            else if (strlen($char) == 2)
            {

                $pos = strpos($zhnumbers, $char);

                if (($pos !== false) && (($pos % 2) == 0))
                {

                    if ($continue)
                    {

                        $numstr .=$zh_ennumbers[$lastpos / 2];

                        $count++;
                    }

                    $lastchar = $char;

                    $lastpos = $pos;

                    $continue = true;
                }
                else
                {

                    if ($lastchar != "")
                    {

                        $count++;

                        if ($lastpos != -1)
                        {

                            $numstr .=$zh_ennumbers[$lastpos / 2];
                        }
                        else
                        {

                            $numstr .=$lastchar;
                        }
                    }

                    if ($count >= 5)
                    {

                        $fullstr .=" " . $numstr;
                    }

                    $lastchar = "";

                    $numstr = "";

                    $count = 0;

                    $lastpos = -1;

                    $continue = false;
                }
            }
            else
            {

                if ($lastchar != "")
                {

                    $count++;

                    if ($lastpos != -1)
                    {

                        $numstr .=$zh_ennumbers[$lastpos / 2];
                    }
                    else
                    {

                        $numstr .=$lastchar;
                    }
                }

                if ($count >= 5)
                {

                    $fullstr .=" " . $numstr;
                }

                $lastchar = "";

                $lastpos = -1;

                $numstr = "";

                $count = 0;

                $continue = false;
            }
        }

        return $fullstr;
    }

    function extractContacts($content, &$teles)
    {

        $patterns = array(
            "/[A-Za-z0-9+]+[A-Za-z0-9\.\_\-+]*@([A-Za-z0-9\-]+\.)+[A-Za-z0-9]+/",
            "/qq:\s*\d{5,}\s*/i",
            "/qq：\s*\d{5,}\s*/i",
            "/\d{3,4}\-?\d{7,8}/",
            "/13\d{9}/",
            "/15[89]\d{8}/",
        );

        foreach ($patterns as $pattern)
        {

            if (preg_match_all($pattern, $content, $result))
            {

                foreach ($result[0] as $tele)
                {

                    $teles[] = $tele;
                }
            }
        }
    }

    function extractEssential($content)
    {

        $zhfilterchars = "着嘛哩的地得了么呢吧啊吗呀哈哪呵哇啰啦　～？、：；，。‘’《》“”｜！｝｛］［〖〗〈〉【】·＃￥％…＆×（）－＝＋—＠";

        $enfilterchars = " ~?\\:;,.'<>/|!#$%^&*()-=_+@\n\t";

        $content = DUtil_Str::removeHTMLtags($content);

        $len = strlen($content);

        $outcontent = "";

        $filter = false;

        for ($i = 0; $i < $len; $i++)
        {

            $filter = false;

            if (ord($content[$i]) > 0x80)
            {

                $char = $content[$i] . $content[$i + 1];

                $i++;

                $pos = strpos($zhfilterchars, $char);

                if (($pos !== false) && (($pos % 2) == 0))
                {

                    $filter = true;
                }
            }
            else
            {

                $char = $content[$i];

                $pos = strpos($enfilterchars, $char);

                if ($pos !== false)
                {

                    $filter = true;
                }
            }

            if (!$filter)
            {

                $outcontent .=$char;
            }
        }

        return $outcontent;
    }

    public static function native2ascii($str)
    {
        $ncr = mb_encode_numericentity($str, array(0x0, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
        $arrPercent = array();
        $arr = explode(';', $ncr);
        foreach ($arr as $one)
        {
            $one = trim($one);
            if (0 == strlen($one))
            {
                continue;
            };
            $dec = str_replace('&#', '', $one);
            $hex = dechex($dec);
            $arrPercent[] = "\\u" . $hex;
        }
        return implode('', $arrPercent);
    }

    //把一个包含汉字的字符串split

    function splitString_keepface($str, $length, &$arr, $betterlastpage=true)
    {

        //换成FACE

        preg_match_all("/<img.*?>/", $str, $ret);



        $facedata = array();

        $repldata = array();

        if (is_array($ret[0]))
        {

            foreach ($ret[0] as $v)
            {

                $item = trim($v, "<>");

                $items = explode(" ", $item);

                $data = array();

                foreach ($items as $v2)
                {

                    list($name, $value) = explode("=", $v2, 2);

                    $name = strtolower($name);

                    $data[$name] = trim($value, "\"'");
                }

                list($domain, $filename) = explode("/i/face/", $data["src"], 2);

                $domain = strtolower($domain);

                if ($domain == "http://" . IMG_HOST)
                {

                    list($no, $suffix) = explode(".", $filename, 2);

                    if ($suffix == "gif" && is_numeric($no))
                    {

                        $facedata[] = $v;

                        $repldata[] = "FACE" . sprintf("%02d", $no);
                    }
                }
            }

            if ($facedata)
            {

                $str = str_replace($facedata, $repldata, $str);
            }
        }



        $strLen = strlen($str);

        $start = 0;

        $strArr = array();

        $i = 0;

        $speciallength = $length - 1;



        while ($start < $strLen)
        {

            $matchData = substr($str, $start, $length);

            $pattern = '/^([\000-\177]|[\200-\377][\100-\176,\200-\377])*([\000-\177]|[\200-\377][\100-\176,\200-\377])$/';

            $suc = preg_match($pattern, $matchData);



            if ($suc)
            {

                $taillength = 0;

                for ($j = 1; $j <= 5; $j++)
                {

                    $tail = substr($matchData, 0 - $j, $j);

                    if (0 == strncmp($tail, "FACE", min($j, 4)))
                    {

                        $taillength = $j;

                        break;
                    }
                }



                $strArr[$i] = substr($str, $start, $length - $taillength);

                $strArr[$i] = str_replace($repldata, $facedata, $strArr[$i]);

                $start += $length - $taillength;
            }
            else
            {

                $strArr[$i] = substr($str, $start, $speciallength);

                $strArr[$i] = str_replace($repldata, $facedata, $strArr[$i]);

                $start += $speciallength;
            }

            $i++;
        }



        //分页截断优化

        $total = count($strArr);

        if ($betterlastpage && $total > 1)
        {

            //访问倒数第二页时，最后一页如果很短则附加到倒数第二页上

            if (strlen($strArr[$total - 1]) < $length / 5)
            {

                $strArr[$total - 2] .= $strArr[$total - 1];

                unset($strArr[$total - 1]);

                $total--;
            }



            for ($i = 0; $i < $total; $i++)
            {

                $content = $strArr[$i];



                if ($i > 0)
                {

                    //抛弃头部被截断标签

                    if (strpos($content, ">") !== false && ((strpos($content, ">") < strpos($content, "<")) || strpos($content, "<") === false))
                    {

                        $content = substr($content, strpos($content, ">") + 1, strlen($content) - strpos($content, ">"));
                    }



                    if (strpos($content, ";") !== false && strpos($content, ";") < 10)
                    {

                        $content = substr($content, strpos($content, ";") + 1, strlen($content) - strpos($content, ";"));
                    }
                }



                if ($i < $total - 1)
                {

                    //补齐尾部被截断标签

                    if (strrpos($content, "<") !== false && ((strrpos($content, "<") > strrpos($content, ">")) || strrpos($content, ">") === false))
                    {

                        $content .= substr($strArr[$i + 1], 0, strpos($strArr[$i + 1], ">") + 1);
                    }



                    if (strpos($strArr[$i + 1], ";") !== false && strpos($strArr[$i + 1], ";") < 10)
                    {

                        $content .= substr($strArr[$i + 1], 0, strpos($strArr[$i + 1], ";") + 1);
                    }
                }



                $strArr[$i] = $content;
            }
        }



        $arr = $strArr;
    }

    //wap笑脸转换

    function html2abstract_keepface2($str, $length, $ext = '')
    {

        $str = DUtil_Str::html2abstract_keepface($str, $length, $ext);

        $str = preg_replace("/<img([^<]*)src=\"([^<]*)\"([^<]*)>/", "<img alt=\"\" src=\"$2\" />", $str);

        return $str;
    }

    /**

     * 清理HTML, 删除危险的标记和变量, 并且清理HTML注释

     * @access private

     * @param string $text

     * @return string

     */
    function removeHTMLtags($text)
    {

        # 删除所有HTML注释

        $text = DUtil_Str::_removeHTMLcomments($text);

        $bits = explode('<', $text);

        $text = array_shift($bits);



        foreach ($bits as $x)
        {

            preg_match('/^(\\/?)(\\w+)([^>]*?)(\\/{0,1}>)([^<]*)$/', $x, $regs);

            @list( $qbar, $slash, $t, $params, $brace, $rest ) = $regs;

            $text .= $rest;
        }

        $text = str_replace("&nbsp;", "", $text);

        $text = str_replace("&nbsp", "", $text);



        $text = DUtil_Str::_removeScripts($text);

        return $text;
    }

    /**

     * 删除'<!--', '-->', 之间的任何标记.

     * @access private

     */
    function _removeHTMLcomments($text)
    {

        while (($start = strpos($text, '<!--')) !== false)
        {

            $end = strpos($text, '-->', $start + 4);

            if ($end === false)
            {

                $text = substr($text, 0, $start);

                break;
            }

            $text = substr($text, 0, $start) . " " . substr($text, $end + 3);
        }

        return $text;
    }

    /**

     * 删除'<script', '/script>', 之间的任何标记.

     * @access private

     */
    function _removeScripts($text)
    {

        while (($start = stripos($text, '<script')) !== false)
        {

            $end = stripos($text, '/script>', $start + 7);

            if ($end === false)
            {

                $text = substr($text, 0, $start);

                break;
            }

            $text = substr($text, 0, $start) . " " . substr($text, $end + 8);
        }

        return $text;
    }

    function html2text($str, $unix = true)
    {

        $nl = "\n";

        if (!$unix)
        {

            $nl = "\r\n";
        }

        $str = str_ireplace("\n", "", $str);

        $str = str_ireplace(array("<p", "<br"), array($nl . "<p", $nl . "<br"), $str);

        $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU", "", $str);

        $alltext = "";

        $start = 1;

        for ($i = 0; $i < strlen($str); $i++)
        {

            if ($start == 0 && $str[$i] == ">")
            {

                $start = 1;
            }
            else if ($start == 1)
            {

                if ($str[$i] == "<")
                {

                    $start = 0;

                    $alltext .= " ";
                }
                else
                {

                    $alltext .= $str[$i];
                }
            }
        }

        $alltext = preg_replace("/&([^;&]*)(;|&)/", "", $alltext);

        return $alltext;
    }

    function getOnlineHtmlEx($uid, $real_name, $online, $mobile = 0, $fromwap = false)
    {

        $img = DUtil_Str::getOnlineHtml($online, $mobile, $fromwap, true, $real_name);

        if ($img)
        {

            if (!$fromwap)
            {

                return '<a href="javascript:g_chatwith(' . intval($uid) . ')">' . $img . '</a>';
            }
        }

        return $img;
    }

    function getOnlineHtml($online, $mobile = 0, $fromwap = false, $canchat=false, $real_name="")
    {

        $imghost = $fromwap ? WAP_IMG_HOST : IMG_HOST;

        $title = "";

        $title = "在线";

        if ($canchat && $real_name != "")
        {

            $title = "跟" . htmlspecialchars($real_name) . "在线聊天";
        }



        if ($online)
        {

            if ($mobile)
            {

                return '<img src="http://' . $imghost . '/i/u_zxm.gif" style="display:inline-block;margin-bottom:-4px;" title="' . $title . '" />';
            }
            else
            {

                return '<img src="http://' . $imghost . '/i/u_zx1.gif" style="display:inline-block;margin-bottom:-4px;" title="' . $title . '" />';
            }
        }

        return '';
    }

    // 全角转半角

    function q2b($string)
    {

        $string = preg_replace('/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $string);

        return $string;
    }

    //在map数组中的特定位置，插入一个值

    function array_map_insert($arr, $pos, $key, $value)
    {

        if ($pos < 0)
        {

            return $arr;
        }



        if ($pos > count($arr))
        {

            return $arr;
        }



        $arr_sort = array_keys($arr);



        for ($i = 0; $i < $pos; $i++)
        {

            $tmpKey = $arr_sort[$i];

            $arr1[$tmpKey] = $arr[$tmpKey];
        }



        $arr1[$key] = $value;



        $acount = count($arr);

        for ($i = $pos; $i < $acount; $i++)
        {

            $tmpKey = $arr_sort[$i];

            $arr1[$tmpKey] = $arr[$tmpKey];
        }



        $arr = $arr1;



        return $arr;
    }

    function filterPath($t)
    {

        $t = str_replace("..", "", $t);

        return trim($t, ".\\/");
    }

    // 去掉html中前部和尾部的空行

    function htmlTrimEmptyLine($html)
    {

        $html = str_replace("<p >&nbsp;</p >", " ", $html);

        $html = str_replace("\n", " ", $html);

        $html = preg_replace("/(\<br[\s\/]*\>\s*)/i", "\n", $html);

        $html = trim($html);

        return nl2br($html);
    }

    // 去掉html中的头尾换行，并将中部连续的2个以上换行转成2个换行

    function htmlRemoveEmptyLine($html)
    {

        $html = str_replace("<p >&nbsp;</p >", "<br />", $html);

        $html = str_replace("<p >", "", $html);

        $html = str_replace("</p >", "<br />", $html);

        $html = str_replace("\n", " ", $html);

        $html = preg_replace("/(\<br[\s\/]*\>\s*)/i", "\n", $html);

        $html = trim($html);

        $html = preg_replace("/\n{2,}/", "\n\n", $html);

        return nl2br($html);
    }

    // htmlspecialchars转换是保留&

    function kaixinhtmlspecialchars($html)
    {

        $html = htmlspecialchars($html, ENT_QUOTES);

        $html = str_replace("&amp;", "&", $html);

        return $html;
    }

    function ymd2date($ymd)
    {

        if (!function_exists("strptime"))
        {

            $year = 2000 + substr($ymd, 0, 2);

            $mon = substr($ymd, 2, 2);

            $day = substr($ymd, 4, 2);

            return $year . "-" . $mon . "-" . $day;
        }
        else
        {

            $arr = strptime($ymd, "%y%m%d");

            $year = 1900 + $arr["tm_year"];

            $month = 1 + $arr["tm_mon"];

            $day = $arr["tm_mday"];

            return sprintf("%s-%02d-%02d", $year, $month, $day);
        }
    }

    function url_base64_encode($str)
    {

        $search = array('+', '/');

        $replace = array('*', '-');

        $basestr = base64_encode($str);

        return str_replace($search, $replace, $basestr);
    }

    function url_base64_decode($str)
    {

        $search = array('*', '-');

        $replace = array('+', '/');

        return base64_decode(str_replace($search, $replace, $str));
    }

    function loadJ2F()
    {
        if (empty(self::$j2f))
        {
            $filename = DATA_PATH . "/f2j.txt";
            $content = file_get_contents($filename);
            $fn_list = explode("\n", $content);
            foreach ($fn_list as $fn)
            {
                $fn = trim($fn);
                if (strlen($fn) != 4)
                {
                    continue;
                }
                self::$j2f[substr($fn, 2, 2)] = substr($fn, 0, 2);
            }
        }
    }

    /*
     * 简体字、繁体字互相组合
     */

    public static function jt_ft($hz)
    {
        $len = strlen($hz);
        $hzstr_arr = array();
        for ($i = 0; $i < $len; $i++)
        {
            $p = ord(substr($hz, $i, 1)); //取一个字节
            if ($p > 128)
            { //gbk汉字
                $p = substr($hz, $i, 2); //取两个字节
                $hzstr_arr[$i / 2] = DUtil_Str::jtft($p);
                $i++; //指针后移
            }
        }
        $n = count($hzstr_arr);
        $merge_arr = $hzstr_arr[0];
        if ($n > 1)
        {
            for ($i = 1; $i < $n; $i++)
            {
                self::mergearr($merge_arr, $hzstr_arr[$i]);
            }
        }
        return $merge_arr;
    }

    public static function mergearr(&$a1, $a2)
    {
        $n1 = count($a1);
        $n2 = count($a2);
        for ($i = 0; $i < $n1; $i++)
        {
            for ($j = 0; $j < $n2; $j++)
            {
                $merge_arr[] = $a1[$i] . $a2[$j];
            }
        }
        $a1 = $merge_arr;
    }

    /**
     * 汉字的简体、繁体组成的数组， added by yanxin
     */
    function jtft($word)
    {
        DUtil_Str::loadJ2F();
        DUtil_Str::loadF2J();

        if (array_key_exists($word, self::$j2f))
        {
            $arr[0] = $word;
            $arr[1] = self::$j2f[$word];
        }
        else if (array_key_exists($word, self::$f2j))
        {
            $arr[0] = $word;
            $arr[1] = self::$f2j[$word];
        }
        else
        {
            $arr[0] = $word;
        }
        return $arr;
    }

    function jt2ft($name)
    {
        DUtil_Str::loadJ2F();
        $arr = DUtil_Str::str2arr($name);
        $acount = count($arr);
        for ($i = 0; $i < $acount; $i++)
        {
            if (array_key_exists($arr[$i], self::$j2f))
            {
                $arr[$i] = self::$j2f[$arr[$i]];
            }
        }
        return implode("", $arr);
    }

}

//CStr提供转换1维数组和2维数组为字符串的功能
//其中 key 和 value 使用uri编码
//支持下面三种数组的转换



/*

  $a1 = array("n1" => "v1", "n2" => "v2");

  $a2 = array("n1" => "v1", "n2" => array("a", "b"));

  $a3 = array(array("n1"=>"v1", "n2"=>"v2"), array("n1"=>"c1", "n2"=>"c2"));



  echo "----------------------------------------\n";



  $s1 = DUtil_Str::a2s($a1);

  echo $s1."\n";

  $a1 = DUtil_Str::s2a($s1);

  var_dump($a1);



  echo "----------------------------------------\n";



  $s2 = DUtil_Str::a2s($a2);

  echo $s2."\n";

  $a2 = DUtil_Str::s2a($s2);

  var_dump($a2);



  echo "----------------------------------------\n";



  $s3 = DUtil_Str::aa2s($a3);

  echo $s3."\n";

  $a3 = DUtil_Str::s2aa($s3);

  var_dump($a3);



  echo "----------------------------------------\n";

 */

/* $content = '有需要买游戏主机及配件的，我能提供给各位，价格不亏就行，记忆棒8GMARK2现120元。欢迎议价！不亏即可！可以送货！PS3 WII 360>促销中～ （WII 1450元 送光盘 WII 双截棍手柄 优惠出售）



  上海闵行交大闵行正面对面东川路上 和枫实体店 谢谢大家支持

  轻轨5号线东川路站下沿东川路向东走10分钟.



  QQ：48850089（本人晚上） 550472461（朋友早上）电话13917162960 13564655476谢谢支持



  http://store.taobao.com/shop/view_shop-dfac3f6315b5a549bc539f329d1df9f3.htm



  webgis@hotmail.com

  39903552@qq.com

  ｖ１３４２６４１１４７２



  （欢迎议价，只要不亏就可以哦）



  http://www.kaixin001.com/group/group.php?gid=249663 有兴趣加入-萌之游戏动漫部落一亿二千三百四十五万六千七百八十九dw

  陆捌六二六一六四

  ';



  $DUtil_Str = DUtil_Str::extractNumbers($content);

  echo $DUtil_Str."\n";

  $array = array();

  DUtil_Str::extractLinks($content, $array);

  print_r($array);

  $mailarray = array();

  DUtil_Str::extractContacts($content, $mailarray);

  $mailarray = array_unique($mailarray);

  print_r($mailarray);

  $outcontent = DUtil_Str::extractEssential($content);

  echo $outcontent."\n"; */
?>