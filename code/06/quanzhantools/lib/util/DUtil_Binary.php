<?php
/*
 * 位操作类
 * 
 * author: xujinglin 
 */

class DUtil_Binary
{
	/*
	 * $value 值
	 * $index 第几位（从0开始）
	 */
	static function get($value, $index)
	{
		return ($value >> $index) & 1;
	}
	
	/*
	 * $value 原始值
	 * $index 第几位
	 * $indexvalue 设置为几（0或者1）
	 */
	static function  set($value, $index, $indexvalue)
	{
		$tmp = 1 << $index;
		$value = $value | $tmp;
		$value = $value - $tmp;
		$value = $value | ($indexvalue << $index);
		if ($value > 2147483647 || $value < -2147483648)
		{	//兼容64位操作系统
			$value = ($value << 32) >> 32;
		}
		return $value;
	}
}
?>