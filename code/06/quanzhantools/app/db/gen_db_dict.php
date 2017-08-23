<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class db extends DCore_BackApp
{

	protected $dbfile;
	static $filemap = array(
		"db.sql" => array("群组配置相关表", 1, "一"),
		"group.sql" => array("普通群组相关表", 2, "二"),
		"clubqun.sql" => array("俱乐部讨论区相关表", 3, "三"),
		"weiqun.sql" => array("微群相关表", 4, "四"),
		"audit.sql" => array("审核相关表", 5, "五")
	);
	
	static $filter_map = array(
		"cg_server_setting" => 1,
		"cg_kind_setting" => 1,
		"cg_table_setting" => 1,
		"cg_id_generator" => 1,
		"cg_memcache_setting" => 1
	);

	protected function getParameter()
	{
		$this->dbfile = $this->getParam("dbfile");
	}

	protected function checkParameter()
	{
		if (strpos($this->dbfile, DS) !== false || !$this->dbfile)
		{
			echo $this->dbfile . " 文件不存在:(";
			exit;
		}
	}

	protected function extractField($line)
	{
		list($start, $field) = explode("(", trim($line, "`) "));

		if (strpos($field, ",") !== false)
		{
			list($field, $left) = explode(",", trim($field, "`) "));
		}
		else
		{
			$field = trim($field, "`) ");
		}

		$field = trim($field, "`)");
		return $field;
	}
	protected function outputFileDict($filename)
	{
		$dbfile = ".." . DS . ".." . DS . "doc" . DS . "" . $filename;
		if (!is_file($dbfile))
		{
			echo $dbfile . " 文件未找到！\n";
			exit;
		}
		$content = file_get_contents($dbfile);
		$newscontent = str_replace("\n", "", $content);
		$origincontent = $content;
		$pattern = "/NAME:\s(.*?)\s(.*?)\n/";
		$tables = array();
		$partsindex = array();

		if (preg_match_all($pattern, $content, $result))
		{
			foreach ($result[1] as $key => $item)
			{
				$partsindex[$item] = strpos($content, $result[0][$key]);
				$tables[$item] = $result[2][$key];
			}
		}
		$fileitem = self::$filemap[$filename];
		echo "<h2>".$fileitem[2]."、".$fileitem[0]."</h2>\n";
		$pattern = "/create\s+table(.*?)\(/i";
		if (preg_match_all($pattern, $newscontent, $result))
		{
			foreach ($result[0] as $key => $item)
			{
				$table_name = trim(str_replace(array("`"), array(""), $result[1][$key]));
				//不输出此表的数据字典
				if(isset(self::$filter_map[$table_name]))
				{
					continue;
				}
				$item = str_replace("(", "", $item);
				$pos = strpos($content, $item);
				if ($pos === false)
				{
					echo $content . "\n";
					exit;
				}
				$content = substr($content, $pos);
				$endpos = strpos($content, "UTF8");

				$filecontent = substr($content, 0, $endpos + 7);
				$leftcontent = substr($content, $endpos + 8);

				$tablecontent = substr($origincontent, $partsindex[$table_name]);
				$startpos = stripos($tablecontent, "create table");
				//echo $startpos;
				$codepos = strpos($tablecontent, "UTF8");
				$tablecontent = substr($tablecontent, $startpos, $codepos - $startpos);
				//echo $tablecontent;
				$lines = explode("\n", $tablecontent);
				$show_table = trim($show_table, "0_");
				if (strpos($table_name, "_0"))
				{
					$split = "是";
				}
				else
				{
					$split = "否";
				}
				$count ++;
				$index = $fileitem[1].".".$count;
				echo "<h3>".$index." " . $table_name . "</h3><br />
					<b>数据表说明：</b>" . $tables[$table_name] . " <br /><br />
					<b>是否是分表：</b>" . $split . "<br /><br />\n";
				echo '<table cellpadding="0" cellspacing="0" border="1"  width="650">
					<tr>
					<td><b>字段名<b></td>
					<td><b>类型<b></td>
					<td><b>可否空<b></td>
					<td><b>字段描述<b></td>
					<td><b>备注<b></td>
					</tr>';
				$fields = array();
				foreach ($lines as $line)
				{
					$line = trim($line);
					if (stripos($line, "create table") !== false
						|| stripos($line, "engine="))
					{
						continue;
					}
					if (strpos($line, "--") !== false)
					{

						list($def, $note) = preg_split("#\-\-\s#", $line);

						list($field, $type) = explode(" ", $def);
						$note = trim($note, "-");
						$field = trim($field, "`");
						$fields[$field] = array(
							"name" => $field,
							"tip" => $note,
							"type" => $type
						);
						if (stripos($line, "not null"))
						{
							$fields[$field]["null"] = "否";
						}
						else if (stripos($line, "null"))
						{
							$fields[$field]["null"] = "是";
						}
						if (stripos($line, "primary key"))
						{
							$fields[$field]["key"] = "主键";
						}
					}
					else
					{
						$field = $this->extractField($line);

						if (stripos($line, "primary") !== false && stripos($line, "("))
						{
							$fields[$field]["key"] = "主键";
						}
						else if (stripos($line, "index") !== false && stripos($line, "("))
						{

							$fields[$field]["key"] = '索引';
						}
						else if (stripos($line, "unique") !== false && stripos($line, "("))
						{

							$fields[$field]["key"] = '唯一键';
						}
						else if (stripos($line, "key") !== false && stripos($line, "("))
						{

							$fields[$field]["key"] = '索引';
						}
						//echo $field."\t".$fields[$field]["key"];
					}
				}
				foreach ($fields as $field => $items)
				{
					echo'
					<tr>
					<td><b>' . $items['name'] . '</b></td>
					<td>' . $items['type'] . '</td>
					<td>' . $items['null'] . '</td>
					<td>' . $items['tip'] . '</td>
					<td>' . $items['key'] . '</td>
					</tr>';
				}
				echo '</table>';
			}
		}
	}
	protected function main()
	{
		if($this->dbfile == "all")
		{
			echo "<h1>群组项目数据字典</h1>\n";
			foreach(self::$filemap as $dbfile => $tips)
			{
				$this->outputFileDict($dbfile);
			}
		}
		else
		{
			$this->outputFileDict($this->dbfile);
		}
	}

}

$db = new db();
$db->run();
?>
