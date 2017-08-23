<?php

/**
 * 进程内缓存
 *
 * @package
 * @subpackage
 */
class DCore_ProCache
{

	private static $_cache = array();

	static public function set($name, $v)
	{
		self::$_cache[$name] = $v;
	}

	static public function get($name, $once=false)
	{
		$v = null;
		if (isset(self::$_cache[$name]))
		{
			$v = self::$_cache[$name];
			if ($once)
				unset(self::$_cache[$name]);
		}
		return $v;
	}

}

?>
