<?php

/**
 * UTF-8乱码转GBK,用于各种怀疑是UTF-8编码的字符串转GBK。
 *      支持Unicode编码的字符串，如"%u4E24%u6027" => "两性"
 *      "&#20020;&#27778;&#20256;&#23186;&#32593;" => "临沂传媒网"
 *                              "&#x4E24;&#x6027;" => "两性"
 */
class DUtil_Convert
{

	static $special = array(
		array(1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1),
		/* 隆垄拢  楼娄    漏陋芦卢颅庐炉    虏鲁麓碌露  赂鹿潞禄录陆戮驴 */
		array(0, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 1)
		/*   芒茫盲氓忙莽      毛    卯茂冒帽    么玫枚  酶    没  媒镁每 */
	);

	static function unescape($str)
	{
		preg_match_all("/(?:%u[0-9A-Fa-f]{4})|&#x[0-9A-Fa-f]{4};|&#\d+;|.+|\n/U", $str, $r);
		$ar = $r[0];
		foreach ($ar as $k => $v)
		{
			if (substr($v, 0, 2) == "%u")
			{
				$ar[$k] = iconv("UCS-2BE", "UTF-8", pack("H4", substr($v, -4)));
			}
			elseif (substr($v, 0, 3) == "&#x")
			{
				$ar[$k] = iconv("UCS-2BE", "UTF-8", pack("H4", substr($v, 3, -1)));
			}
			elseif (substr($v, 0, 2) == "&#")
			{
				$ar[$k] = iconv("UCS-2BE", "UTF-8", pack("n", substr($v, 2, -1)));
			}
		}
		return join("", $ar);
	}

	function contain_special($str)
	{
		$strlen = strlen($str);
		for ($i = 0; $i < $strlen - 1;)
		{
			if (ord($str[$i]) & 0x80 != 0)
			{
				if ($i + 1 < $strlen)
				{
					if (ord($str[$i]) >= 0xc2 && ord($str[$i]) <= 0xc3)
					{
						if (ord($str[$i + 1]) >= 0xa1 && ord($str[$i + 1]) <= 0xbf)
						{
							if (self::$special[ord($str[$i]) - 0xc2][ord($str[$i + 1]) - 0xa1])
								return TRUE;
							else
								$i = $i + 2;
						}
					}
				}
			}
			$i++;
		}

		return FALSE;
	}

	function junk2gbk($str, $safe = true, $cut = 0)
	{
		$tempstr = $this->unescape($str);
		if ($cut)
		{
			$tempstr = substr($tempstr, 0, 0 - $cut);
		}
		$gbkstr = iconv(SYS_CHARSET, DB_CHARSET, $tempstr);
		if (DUtil_Convert::iconvFailure($gbkstr, $tempstr))
		{
			$cut++;
			if ($cut < 5 && strlen($str) > 4)
			{ //截断末尾的1到4个字符在进行转换
				return $this->junk2gbk($str, $safe, $cut);
			}
			//判断是否包含特殊字符，包含的不进行转码
			if ($this->contain_special($tempstr) == TRUE)
			{
				return $safe ? DParse_HtmlParse::forbidScript($tempstr) : $tempstr;
			}
			$gbkstr = iconv(SYS_CHARSET, "8859_1", $tempstr);
			if (DUtil_Convert::iconvFailure($gbkstr, $tempstr))
			{
				return $safe ? DParse_HtmlParse::forbidScript($tempstr) : $tempstr;
			}
		}
		else
		{
			/* 转码前后长度一样的，认为不是UTF-8编码
			  (gb18030含有部分4字节字符，转成utf-8长度有可能一样)
			  if (strlen($gbkstr) == strlen($tempstr))
			  {
			  return $safe ? DParse_HtmlParse::forbidScript($tempstr) : $tempstr;
			  } */

			//部分转码认为不是UTF-8编码
			if (strlen($gbkstr) && strstr($tempstr, $gbkstr) !== FALSE)
			{
				return $safe ? DParse_HtmlParse::forbidScript($tempstr) : $tempstr;
			}
		}

		return $safe ? DParse_HtmlParse::forbidScript($gbkstr) : $gbkstr;
	}

	function iconvFailure($ret, $str)
	{
		return $ret === false || ($ret === "" && strlen($str));
	}
	static function GBKtoUTF8($input)
	{
		return iconv("GB18030", "UTF-8". "//IGNORE", $input);
	}
	static function UTF8toGBK($input)
	{
		return iconv("UTF-8", "GB18030" . "//IGNORE", $input);
	}
	static function getSysStr($input)
	{
		return iconv(DB_CHARSET, SYS_CHARSET . "//IGNORE", $input);
	}

	static function getDBStr($input)
	{
		return iconv(SYS_CHARSET, DB_CHARSET . "//IGNORE", $input);
	}

	//因为junk2gbk方法会丢英文字符或数字后面的汉字，所以改写了一个转换函数，目前用于rshare组件.
	function junk2gbkRshare($value)
	{
		$gbklen = iconv_strlen($value, 'GB18030');
		$utf8len = iconv_strlen($value, 'UTF-8');
		$oldValue = $value;
		if ($gbklen == $utf8len and $utf8len > 0)
		{ //目前发现的特殊字符串,不用转码: ‘什么sdfsd’
			$value = $oldValue;
		}
		else if ($utf8len)
		{ //utf8编码
			$value = iconv("UTF-8", "GB18030", $value);
		}
		else if ($gbklen === false)
		{//即不是UTF8也不是GB编码
			$value = $this->junk2gbk($value);
		}
		//gbk无须转码

		if (empty($value))
		{//万一判断错误转码失败（有可能GBK串里面包含几个UTF8字符），用原值。
			$value = $oldValue;
		}
		return $value;
	}

	//filter Private Use Zone Characters
	static function filter_PUZ($str)
	{
		//起始字符和终止字符
		$begin = 0xE000;		//57344
		$end = 0xF8FF;		  //63743
		//转化成数字格式，以方便判定
		$ncr = mb_encode_numericentity($str, array(0x0, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
		//echo $ncr."\n";
		$len = strlen($ncr);
		$start = 0;
		$index = 0;
		$total = 0;
		$left = "";
		$haspuz = false;
		//一遍字符串扫描完成所有操作
		do
		{
			$total++;
			$index++;
			if ($ncr[$index] == ';')
			{
				$value = intval(substr($ncr, 2, $index - $start));
				$ncr = substr($ncr, $index + 1);
				$total++;
				if ($value >= $begin && $value <= $end)
				{
					$haspuz = true;
				}
				else
				{
					$left .= "&#" . $value . ";";
				}
				$index = 0;
			}
		}
		while ($total < $len);
		//存在PUZ字符
		if ($haspuz)
		{
			return mb_decode_numericentity($left, array(0x0, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
		}
		else
		{
			//如果根本无PUZ字符，原样返回
			return $str;
		}
	}

	//不丢失字符转gbk，不能转的字符用&#174;这种unicode形式表示。
	//输出到页面时要防止&#174;这种字符做htmlspecialchars，需要的话先转utf8然后unescape再htmlspecialchars
	static function tryGetGBK($name)
	{
		$tostr = "";
		for ($i = 0; $i < strlen($name); $i++)
		{
			$curbin = ord(substr($name, $i, 1));
			if ($curbin < 0x80)
			{
				$tostr .= substr($name, $i, 1);
			}
			elseif ($curbin < bindec("11000000"))
			{
				$str = substr($name, $i, 1);
				$tostr .= "&#" . ord($str) . ";";
			}
			elseif ($curbin < bindec("11100000"))
			{
				$str = substr($name, $i, 2);
				$tostr .= "&#" . self::getUnicodeChar($str) . ";";
				$i += 1;
			}
			elseif ($curbin < bindec("11110000"))
			{
				$str = substr($name, $i, 3);
				$gstr = iconv("UTF-8", "GB18030", $str);
				if (!$gstr)
				{
					$tostr .= "&#" . self::getUnicodeChar($str) . ";";
				}
				else
				{
					$tostr .= $gstr;
				}
				$i += 2;
			}
			elseif ($curbin < bindec("11111000"))
			{
				$str = substr($name, $i, 4);
				$tostr .= "&#" . self::getUnicodeChar($str) . ";";
				$i += 3;
			}
			elseif ($curbin < bindec("11111100"))
			{
				$str = substr($name, $i, 5);
				$tostr .= "&#" . self::getUnicodeChar($str) . ";";
				$i += 4;
			}
			else
			{
				$str = substr($name, $i, 6);
				$tostr .= "&#" . self::getUnicodeChar($str) . ";";
				$i += 5;
			}
		}
		return $tostr;
	}

	static function getUnicodeChar($str)
	{
		$temp = "";
		for ($i = 0; $i < strlen($str); $i++)
		{
			$x = decbin(ord(substr($str, $i, 1)));
			if ($i == 0)
			{
				$s = strlen($str) + 1;
				$temp .= substr($x, $s, 8 - $s);
			}
			else
			{
				$temp .= substr($x, 2, 6);
			}
		}
		return bindec($temp);
	}

}

?>