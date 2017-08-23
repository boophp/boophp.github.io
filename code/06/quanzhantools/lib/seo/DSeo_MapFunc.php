<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DSeo_MapFunc
{

	public static function parse_list_uri($uri, &$pars)
	{
		if (strpos($uri, "/list") === 0)
		{
			list($method, $tagid, $tagname) = explode("/", trim($uri, "/"));
			if ($tagid)
			{
				$pars["tgid"] = $tagid;
			}
			if ($tagname)
			{
				$pars["tname"] = $tagname;
			}
			return "/list.php";
		}
		return "";
	}
	public static function parse_overseas_uri($uri, &$pars)
	{
		if (strpos($uri, "/overseas") === 0)
		{
			list($method, $start, $num) = explode("/", trim($uri, "/"));
			if ($start)
			{
				$pars["start"] = $start;
			}
			if ($num)
			{
				$pars["num"] = $num;
			}
			$pars['rectype'] = DMaiyou_Const::MAIYOU_RECOMMEND_TYPE_OVERSEAS;
			return "/index.php";
		}
		return "";
	}
	public static function parse_index_uri($uri, &$pars)
	{
		if (strpos($uri, "/index") === 0)
		{
			list($method, $start, $num) = explode("/", trim($uri, "/"));
			if ($start)
			{
				$pars["start"] = $start;
			}
			if ($num)
			{
				$pars["num"] = $num;
			}
			return "/index.php";
		}
		return "";
	}
	
	
	public static function parse_detail_uri($uri, &$pars)
	{
		if (strpos($uri, "/detail") === 0)
		{
			list($method, $mtitle, $gid) = explode("/", trim($uri, "/"));
			if ($start)
			{
				$pars["mtitle"] = $mtitle;
			}
			if ($gid)
			{
				$pars["gid"] = $gid;
			}
			return "/detail.php";
		}
		return "";
	}

}

?>
