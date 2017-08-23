<?php

/**
 * 业务数据库操作的进一步封装
 *
 * @package
 * @subpackage
 */
/**
 * @return 返回指定 kind 分配到的 ID，如果 Kind 没有配置则抛出异常
 * 分配 ID 用 memcached 加锁，同一Kind，在同一时间，只能由同一个人所分配，否则只能等待
 * 1秒后再求分配，两次后如仍未成功，则出异常
 *
 * @param type $kind 
 */
class DDb_IDGen
{

	private static function getNewIntId($kind)
	{
		 $key = "gen_id_" . $kind;
 
		$count = 0;
		do
		{
			$val = DCache_Memcache::get($key);
			if (!$val)
			{
				$val =  DCache_Memcache::set("gen_id_" . $kind, 1, 60);
				$row = DDb_DBConfig::getTableRow(DDb_Const::ID_TABLE_KIND, array("kind" => $kind));
				if (!$row || empty($row))
				{
					$id = 1;
					$arr = array(
						"kind" => $kind,
						"last_id" => $id
					);
					DDb_DBConfig::insert(DDb_Const::ID_TABLE_KIND, $arr);
				}
				else
				{
					$id = $row[0]["last_id"] + 1;
					$arr = array(
						"last_id" => $id
					);
					DDb_DBConfig::update(DDb_Const::ID_TABLE_KIND, $arr, "kind='" . mysql_escape_string($kind) . "'");
				}
				 DCache_Memcache::set($key, 0);
				return $id;
			}
			else
			{
				$count++;
				if ($count > 2)
				{
					$code = DExcept_Const::DB_EXCEPTION_CODE_ID_TIMEOUT;
					throw new DExcept_DBException($code);
				}
				sleep(1);
			}
		}
		while (1);
	}

	public static function getNewId($kind)
	{
		if (!isset(DDb_Const::$id_map[$kind]))
		{
			$code = DExcept_Const::DB_EXCEPTION_CODE_ID_KIND_ERROR;
			$msg = DExcept_Const::$msg_map[$code];
			$msg = sprintf($msg, $kind);
			throw new DExcept_DBException($code, $msg);
		}
		$type = DDb_Const::$id_map[$kind];
		if ($type == "int")
		{
			return self::getNewIntId($kind);
		}
		else if ($type == "time")
		{
			return self::getNewTimeId($kind);
		}
		$code = DExcept_Const::DB_EXCEPTION_CODE_ID_KIND_ERROR;
		throw new DExcept_DBException($code);
	}

	/**
	 * 按时间取得的Id，无需要锁定。
	 * 
	 * @param type $kind 
	 */
	private static function getNewTimeId($kind)
	{
		list($usec, $sec) = explode(" ", microtime());

		$timeid = strval($sec) . "" . strval($usec);
		$timeid = str_replace("0.", "", $timeid);
		$arr = array(
			"kind" => $kind,
			"last_id" => $timeid
		);
		DDb_DBConfig::replace(DDb_Const::ID_TABLE_KIND, $arr);
	}

	/**
	 * @return 返回指定 kind 当前的最大的 ID，如果 Kind 不存在则为 0
	 * @param type $kind 
	 */
	public static function getLastId($kind)
	{
		$row = DDb_DBConfig::getTableRow(DDb_Const::ID_TABLE_KIND, array("kind" => $kind));
		if (!$row || empty($row))
		{
			return 0;
		}
		else
		{
			return $row[0]["last_id"];
		}
	}

}