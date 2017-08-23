<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DSeo_Router
{
	static function requireURI($url, $pars)
	{
		if (is_string($pars) && $pars)
		{
			$pars = DUtil_Str::s2a($pars);
		}
		foreach ($pars as $key => $val)
		{
			$_REQUEST[$key] = $val;
		}
		//var_dump($pars);
		$url = ROOT_DIR . "" . $url;
		//var_dump($url);
		if (is_file($url))
		{
			require_once($url);
		}
		else
		{
			require_once(ROOT_DIR . "/interface/404.php");
		}
	}
}
?>
