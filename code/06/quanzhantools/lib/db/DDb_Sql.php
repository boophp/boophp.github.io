<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * SQL 语句拼接
 * 
 * @author jinglin
 */
class DDb_Sql
{

	static function insert($arr, $table, $updatefield = array(), $changefield = array())
	{

		$sql = "INSERT INTO $table (";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k;
			$flag = 1;
		}
		$sql .= ") VALUES(";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "'" : ", '") . mysql_escape_string($v) . "'";
			$flag = 1;
		}
		$sql .= ")";
		if (!empty($updatefield) || !empty($changefield))
		{
			$sql .= " ON DUPLICATE KEY UPDATE ";
			$flag = 0;
			foreach ($updatefield as $v)
			{
				$sql .= ($flag == 0 ? "" : ", ") . $v . " = '" . mysql_escape_string($arr[$v]) . "'";
				$flag = 1;
			}
			foreach ($changefield as $k => $v)
			{
				if (is_numeric($v))
				{
					$sql .= ($flag == 0 ? "" : ", ") . $k . "=" . $k . ($v >= 0 ? "+" : "") . ($v);
				}
				else
				{
					$sql .= ($flag == 0 ? "" : ", ") . $k . "=CONCAT(" . $k . ",'" . mysql_escape_string($v) . "')";
				}
				$flag = 1;
			}
		}

		return $sql;
	}

	//向同一个表批量插入大量数据的时候，用这个函数效率高
	static function insertMore($arr, $table)
	{

		$tmpArr = $arr[0];

		$sql = "INSERT INTO $table (";
		$flag = 0;
		foreach ($tmpArr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k;
			$flag = 1;
		}
		$sql .= ") VALUES";
		foreach ($arr as $varr)
		{
			$flag = 0;
			$sql.="(";
			foreach ($varr as $k => $v)
			{
				$sql .= ($flag == 0 ? "'" : ", '") . mysql_escape_string($v) . "'";
				$flag = 1;
			}
			$sql .= "),";
		}
		$sql = trim($sql, ' ,');

		return $sql;
	}

	static function replace($arr, $table)
	{
		$sql = "REPLACE INTO $table (";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k;
			$flag = 1;
		}
		$sql .= ") VALUES(";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "'" : ", '") . mysql_escape_string($v) . "'";
			$flag = 1;
		}
		$sql .= ")";

		return $sql;
	}

	//向同一个表批量插入大量数据的时候，用这个函数效率高
	static function replaceMore($arr, $table)
	{

		$tmpArr = $arr[0];

		$sql = "REPLACE INTO $table (";
		$flag = 0;
		foreach ($tmpArr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k;
			$flag = 1;
		}
		$sql .= ") VALUES";
		foreach ($arr as $varr)
		{
			$flag = 0;
			$sql.="(";
			foreach ($varr as $k => $v)
			{
				$sql .= ($flag == 0 ? "'" : ", '") . mysql_escape_string($v) . "'";
				$flag = 1;
			}
			$sql .= "),";
		}
		$sql = trim($sql, ' ,');

		return $sql;
	}

	static function update($arr, $table, $where)
	{

		$sql = "UPDATE $table SET ";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k . " = '" . mysql_escape_string($v) . "'";
			$flag = 1;
		}
		$sql .= " " . $where;

		return $sql;
	}

	static function updateEx($arr, $change, $table, $where)
	{
		$sql = "UPDATE $table SET ";
		$flag = 0;
		foreach ($arr as $k => $v)
		{
			$sql .= ($flag == 0 ? "" : ", ") . $k . " = '" . mysql_escape_string($v) . "'";
			$flag = 1;
		}
		foreach ($change as $k => $v)
		{
			if (is_numeric($v))
			{
				$sql .= sprintf("%s %s=%s%s%s", ($flag == 0 ? "" : ","), $k, $k, ($v >= 0 ? "+" : " "), $v);
				$flag = 1;
			}
			else
			{
				$sql .= sprintf("%s %s=CONCAT(%s,'%s')", ($flag == 0 ? "" : ","), $k, $k, mysql_escape_string($v));
				$flag = 1;
			}
		}
		$sql .= " " . $where;

		return $sql;
	}

	static public function selectOne($table, $condition, $logic="AND", $fields="*")
	{
		return self::select($table, array(
				'condition' => $condition,
				'one' => true,
				'logic' => $logic,
				"select"=>$fields
			));
	}

	static public function select($table, $options=array())
	{
	 	$condition = isset($options['condition']) ? $options['condition'] : null;
		$one = isset($options['one']) ? $options['one'] : false;
		$offset = isset($options['offset']) ? abs(intval($options['offset'])) : 0;
		if ($one)
		{
			$size = 1;
		}
		else
		{
			$size = isset($options['size']) ? abs(intval($options['size'])) : null;
		}
		$select = isset($options['select']) ? $options['select'] : '*';
		if(isset($options['order']) && $options['order'] && strtolower(substr(trim($options['order']), 0, 5)) != "order")
		{
			$options['order'] = "ORDER BY ".$options['order'] ;
		}
		$order = isset($options['order']) ? $options['order'] : null;
		$cache = isset($options['cache']) ? abs(intval($options['cache'])) : 0;
		$logic = isset($options['logic']) ? $options['logic'] : "AND";
		
		$condition = self::buildCondition($condition, $logic);
		$condition = (null == $condition) ? null : "WHERE $condition";

		$limitation = $size ? "LIMIT $offset,$size" : null;

		$sql = "SELECT {$select} FROM `$table` $condition $order $limitation";
		return $sql;
	}

	static public function buildCondition($condition=array(), $logic='AND')
	{
		if (is_string($condition) || is_null($condition))
			return $condition;

		$logic = strtoupper($logic);
		$content = null;
		foreach ($condition as $k => $v)
		{
			$v_str = null;
			$v_connect = '=';

			if (is_numeric($k))
			{
				$content .= $logic . ' (' . self::buildCondition($v, $logic) . ')';
				continue;
			}

			$maybe_logic = strtoupper($k);
			if (in_array($maybe_logic, array('AND', 'OR')))
			{
				$content .= $logic . ' (' . self::buildCondition($v, $maybe_logic) . ')';
				continue;
			}

			if (is_numeric($v))
			{
				$v_str = $v;
			}
			else if (is_null($v))
			{
				$v_connect = ' IS ';
				$v_str = ' NULL';
			}
			else if (is_array($v))
			{
				if (isset($v[0]))
				{
					$v_str = null;
					foreach ($v AS $one)
					{
						if (is_numeric($one))
						{
							$v_str .= ',' . $one;
						}
						else
						{
							$v_str .= ',\'' . mysql_escape_string($one) . '\'';
						}
					}
					$v_str = '(' . trim($v_str, ',') . ')';
					$v_connect = 'IN';
				}
				else if (empty($v))
				{
					$v_str = $k;
					$v_connect = '<>';
				}
				else
				{
					$v_connect = array_shift(array_keys($v));
					$v_s = array_shift(array_values($v));
					$v_str = "'" . mysql_escape_string($v_s) . "'";
					$v_str = is_numeric($v_s) ? $v_s : $v_str;
				}
			}
			else
			{
				$v_str = "'" . mysql_escape_string($v) . "'";
			}

			$content .= " $logic `$k` $v_connect $v_str ";
		}

		$content = preg_replace('/^\s*' . $logic . '\s*/', '', $content);
		$content = preg_replace('/\s*' . $logic . '\s*$/', '', $content);
		$content = trim($content);

		return $content;
	}

	static public function delete($table=null, $condition = array())
	{
		if (null == $table || empty($condition))
			return false;

		$condition = self::buildCondition($condition);
		$condition = (null == $condition) ? null : "WHERE $condition";
		$sql = "DELETE FROM `$table` $condition";
		return $sql;
	}

}

?>