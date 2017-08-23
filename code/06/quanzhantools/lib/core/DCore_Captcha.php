<?php
/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */
/**
 * 简单验证码工具类
 *
 * @package
 * @subpackage
 */
class DCore_Captcha
{
	const RAND_LENGTH = 4;

	static public function getCaptchaText($rand , $len)
	{
		$ip = getenv('REMOTE_ADDR');
		$svalue = "";
		$sname = "";	 
		 
		$time = intval(time() / 300);

		$key = md5($ip . $svalue . $time . $rand);
		
		$start = rand(0,27);		
		$codevalue = substr($key, $start, self::RAND_LENGTH);

		$memkey = time() . rand(1000,9999);
		DCache_Memcache::set($memkey, $codevalue, 600);
		
		return array ('memkey' => $memkey, 'codevalue'=> $codevalue);
	}

}

?>
