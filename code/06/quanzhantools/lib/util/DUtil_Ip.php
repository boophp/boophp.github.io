<?php

class DUtil_Ip
{

	static function getPlatform()
	{
		$cmd = "uname -a";
		$output = array();
		$platinfo = exec($cmd, $output);
		if (false !== strpos($platinfo, "x86_64"))
		{
			return "64";
		}
		return "32";
	}

	static function getInnerIP()
	{
		$devices = exec("/sbin/ip addr|grep '^[0-9]'|awk '{print $2}'|sed s/://g|tr '\n' ' '");
		$device = explode(' ', $devices);
		foreach ($device as $dev)
		{
			if ($dev == 'lo')
			{
				continue;
			}
			$ip = self::getLocalIp($dev);
			if (self::isInnerIP($ip))
			{
				return $ip;
			}
		}
	}

	static function getLocalIp($interface = "eth0")
	{
		$str = exec("/sbin/ifconfig " . $interface . " | grep 'inet addr'");
		$str = explode(":", $str, 2);
		$str = explode(" ", $str[1], 2);
		return $str[0];
	}

	static function getInnerIP2()
	{
		$ip = self::getLocalIp("eth0");
		if (!self::isInnerIP($ip))
		{
			$ip = self::getLocalIp("eth1");
			if (!self::isInnerIP($ip))
			{
				$ip = self::getLocalIp("bond0");
				if (!self::isInnerIP($ip))
				{
					$ip = self::getLocalIp("bond1");
					if (!self::isInnerIP($ip))
					{
						$ip = "unknown";
					}
				}
			}
		}
		return $ip;
	}

	static function isInnerIP($ip)
	{
		if ($ip == "127.0.0.1")
		{
			return true;
		}
		list($i1, $i2, $i3, $i4) = explode(".", $ip, 4);
		return ($i1 == 10 || ($i1 == 172 && 16 <= $i2 && $i2 < 32) || ($i1 == 192 && $i2 == 168));
	}

	static function getOuterIP($str, $reverse = false)
	{
		$ips = preg_split("/;|,|\s/", $str);
		if ($reverse)
		{
			$ips = array_reverse($ips);
		}
		$rip = "unknown";
		foreach ($ips as $ip)
		{
			$ip = trim($ip);
			if (ip2long($ip) === false)
			{
				continue;
			}
			if (!self::isInnerIP($ip))
			{
				return $ip;
			}
			else
			{
				$rip = $ip;
			}
		}
		return $rip;
	}

	public static function getIP()
	{
		return self::_getIP();
	}

	public static function getIP2()
	{
		return self::_getIP(true);
	}

	private static function _getIP($reverse = false)
	{
		$fip = getenv('HTTP_X_FORWARDED_FOR');
		$oip = self::getOuterIP($fip, $reverse);
		if ($oip != "unknown")
		{
			return $oip;
		}

		$rip = getenv('REMOTE_ADDR');
		return self::getOuterIP($rip, $reverse);
	}

	static function checkIp($ip, $range)
	{
		list($range, $num) = explode("/", $range, 2);
		$num = intval($num);
		$range = ip2long($range);
		$ip = ip2long($ip);

		if ($num >= 32 || $num <= 0)
		{
			return $range == $ip;
		}
		else
		{
			$range = $range >> (32 - $num);
			$ip = $ip >> (32 - $num);
			return $range == $ip;
		}
	}

	static function checkIpEx($ip, $ranges)
	{
		foreach ($ranges as $range)
		{
			if (self::checkIp($ip, $range))
			{
				return true;
			}
		}
		return false;
	}

	static function getCallerIp()
	{
		$myip = $_SERVER['SERVER_ADDR'];
		if ($myip == '' || $myip == "127.0.0.1")
		{
			$filename = DATA_PATH . "/localip";
			if (is_file($filename))
			{
				$myip = trim(file_get_contents($filename));
			}
			else if ($myip == '')
			{
				$myip = "unknown";
			}
		}
		return $myip;
	}
	
	static function getCityByIp($ip)
	{
		$url = "http://www.ip138.com/ips8.asp?ip=$ip&action=2";
		$contents = file_get_contents($url);
                $contents = iconv("gb2312", "UTF-8", $contents);
		preg_match_all('|<li>本站主数据：.*</li>|', $contents, $arr_preg);
                $arr_preg[0][0] = str_replace("<li>本站主数据：", "", $arr_preg[0][0]);
                $pos = strpos($arr_preg[0][0],'</li>');
                $city = substr_replace($arr_preg[0][0],'',$pos);
		return $city;
	}
}

?>
