<?php

/**
 * 操作数据库的原生方法
 *
 * @package
 * @subpackage
 */
class DDb_DBRaw
{

	public static function getTableRow($db, $table, $condition, $logic="AND", $fields="*")
	{
		$sql = DDb_Sql::selectOne($table, $condition, $logic, $fields);
	 
		$ret = $db->getData($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function getTableRowsWithOptions($db, $table, $options)
	{

		if (!isset($options['where']))
		{
			$code = DExcept_Const::DB_EXCEPTION_CODE_INVALID_WHERE;
			throw new DExcept_DBException($code);
		}
		$where = "";
		if ($options['where'])
		{
			$where = "WHERE " . $options['where'];
		}

		$order =trim($options['order']) ? "ORDER BY " . $options['order'] : "";
		$start = $options['offset'] ? $options['offset'] : "0";
		$num = $options['size'] ? $options['size'] : "";
		if ($num)
		{
			$limit = "LIMIT " . $start . "," . $num;
		}
		$fields = "*";
		if ($options['fields'])
		{
			$fields = implode(",", $options['fields']);
		}
		$sql = "SELECT " . $fields . " FROM " . $table . " " . $where . " " . $order . " " . $limit;

		$ret = $db->getData($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function getTableRows($db, $table, $condition, $fields="*")
	{		
		$sql = DDb_Sql::select($table, array("condition" => $condition, "select"=>$fields));
	  
		$ret = $db->getData($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function getTableLimitRows($db, $table, $condition, $start = 0, $num = 20, $order = "", $fields="*")
	{
  
		$sql = DDb_Sql::select($table, array("condition" => $condition,
				"offset" => $start,
				"size" => $num,
				"order" => $order,
				"select" => $fields));
		$ret = $db->getData($sql);
 
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function update($db, $table, $arr, $where)
	{

		$sql = DDb_Sql::update($arr, $table, " WHERE " . $where);
		$ret = $db->runSql($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}
	
	public static function updateEx($db, $table, $arr, $change, $where)
	{

		$sql = DDb_Sql::updateEx($arr, $change, $table, " WHERE " . $where);
		//echo $sql;
		$ret = $db->runSql($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function insert($db, $table, $arr, $updatefield = array(), $changefield = array())
	{
		$sql = DDb_Sql::insert($arr, $table, $updatefield, $changefield);
 
		$ret = $db->runSql($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		$lastid = $db->lastId();
		if ($lastid)
		{
			return $lastid;
		}
		return $ret;
	}

	public static function replace($db, $table, $arr)
	{

		$sql = DDb_Sql::replace($arr, $table);
		$ret = $db->runSql($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException( DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}

	public static function delete($db, $table, $condition)
	{

		$sql = DDb_Sql::delete($table, $condition);
  
		$ret = $db->runSql($sql);
		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret;
	}
    
	public static function count($db, $table, $condition=null, $sum=null)
	{

		$condition = DDb_Sql::buildCondition($condition);
		$condition = null == $condition ? null : "WHERE $condition";
		$zone = $sum ? "SUM({$sum})" : "COUNT(1)";
		$sql = "SELECT {$zone} AS count FROM `$table` $condition";
		$ret = $db->getData($sql);

		$code = $db->errno();
		if ($code)
		{
			throw new DExcept_DBException( DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
		}
		return $ret[0]["count"];
	}
    
    public function doSql($db, $sql)
    {
        $ret = $db->runSql($sql);
        $code = $db->errno();
        
        if ($code)
        {
            throw new DExcept_DBException(DExcept_Const::DB_EXCEPTION_CODE_ERROR + $code, $db->error());
        }
        return $ret;
    }
    
    public function getData($db, $sql)
    {
        return $db->getData($sql);
    }
}

?>
