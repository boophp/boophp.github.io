<?php

/**
 * 配置数据库操作的封装
 *
 * @package
 * @subpackage
 */
class DDb_DBConfig
{

	private static $_mdbinstance = null;
	private static $_sdbinstance = null;

	public static function getInstance($type="master")
	{
		global $CONFS;
		$dbinstance = null;

		if ($type == "master")
		{
			if (empty(self::$_mdbinstance))
			{
				if (defined("SAE_ACCESSKEY"))
				{
					self::$_mdbinstance = new SaeMysql(false); ////new DDb_SaeHandle(true);
				}
				else
				{
					self::$_mdbinstance = new DDb_Handle($CONFS['db']['host']['m'], $CONFS['db']['port'], $CONFS['db']['user'], $CONFS['db']['pass'], $CONFS['db']['name'], "UTF8");
				}
				if ($CONFS['db']['host']['m'] == $CONFS['db']['host']['s'])
				{
					self::$_sdbinstance = self::$_mdbinstance;
				}
			}                            
			return self::$_mdbinstance;
		}
		else if ($type == "slave")
		{
			if (empty(self::$_sdbinstance))
			{
				if (defined("SAE_ACCESSKEY"))
				{
					self::$_sdbinstance = new SaeMysql(false); //new DDb_SaeHandle(false);
				}
				else
				{
					self::$_sdbinstance = new DDb_Handle($CONFS['db']['host']['s'], $CONFS['db']['port'], $CONFS['db']['user'], $CONFS['db']['pass'], $CONFS['db']['name'], "UTF8");
				}
				if ($CONFS['db']['host']['m'] == $CONFS['db']['host']['s'])
				{
					self::$_mdbinstance = self::$_sdbinstance;
				}
			}

			return self::$_sdbinstance;
		}
	}
    
          public static function closeInstance()
          {
              if(self::$_sdbinstance)
              {
                  self::$_sdbinstance->closeDb();
                  self::$_sdbinstance = null;
              }
              
              if(self::$_mdbinstance)
              {
                  self::$_mdbinstance->closeDb();
                  self::$_mdbinstance = null;
              }
          }

	public static function getTableRow($table, $condition, $logic="AND")
	{
		$db = self::getInstance("slave");
		$ret = DDb_DBRaw::getTableRow($db, $table, $condition);
		return $ret;
	}

	public static function getTableRowsWithOptions($table, $options)
	{
		$db = self::getInstance("slave");
		$ret = DDb_DBRaw::getTableRowsWithOptions($db, $table, $options);
		return $ret;
	}

	public static function getTableRows($table, $condition)
	{
		$db = self::getInstance("slave");
		$ret = DDb_DBRaw::getTableRows($db, $table, $condition);
		return $ret;
	}

	public static function getTableLimitRows($table, $condition, $start = 0, $num = 20, $order = "")
	{
		$db = self::getInstance("slave");
		$ret = DDb_DBRaw::getTableLimitRows($db, $table, $condition, $start, $num, $order);
		return $ret;
	}

	public static function update($table, $arr, $where)
	{
		$db = self::getInstance("master");
		$ret = DDb_DBRaw::update($db, $table, $arr, $where);
		return $ret;
	}

	public static function insert($table, $arr, $updatefield = array(), $changefield = array())
	{
		$db = self::getInstance("master");         
                  //   print_r($db);
		$ret = DDb_DBRaw::insert($db, $table, $arr, $updatefield, $changefield);
		return $ret;
	}

	public static function replace($table, $arr)
	{
		$db = self::getInstance("master");
		$ret = DDb_DBRaw::replace($db, $table, $arr);
		return $ret;
	}

	public static function delete($table, $condition)
	{
		$db = self::getInstance("master");
		$ret = DDb_DBRaw::delete($db, $table, $condition);
		return $ret;
	}

	public static function count($table, $condition=null, $sum=null)
	{
		$db = self::getInstance("slave");
		$ret = DDb_DBRaw::count($db, $table, $condition, $sum);
		return $ret;
	}

	
}

?>
