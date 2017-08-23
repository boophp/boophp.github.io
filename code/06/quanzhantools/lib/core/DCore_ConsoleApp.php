<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * 后台应用程序基类
 *
 * @package
 * @subpackage
 */
class DCore_ConsoleApp extends DCore_BaseApp
{
	protected function getParam($pName, $pDefault="")
	{
		$ret = "";
		$data = $_SERVER['argv'];
		$param = array();
		foreach ($data as $v)
		{
			$t = explode('=', $v, 2);
			if (count($t) == 2)
			{
				$param[$t[0]] = $t[1];
			}
		}
		if (isset($param[$pName]))
		{
			$ret = $param[$pName];
		}

		if (!$ret && $pDefault !== "")
		{
			$ret = $pDefault;
		}
		return $ret;
	}
	

}

?>
