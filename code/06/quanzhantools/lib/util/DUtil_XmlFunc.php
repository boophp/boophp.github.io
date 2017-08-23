<?php

class DUtil_XmlFunc
{

	static function  xml2arr($xml, $keys, $noiconv = false)
	{
		$xml_parser = xml_parser_create();

		if (0 === xml_parse_into_struct($xml_parser, $xml, $values, $index))
		{
			xml_parser_free($xml_parser);
			return false;
		}

		$ret = array();

		foreach ($keys as $key)
		{
			$ukey = strtoupper($key);
			if (is_array($index[$ukey]))
			{
				foreach ($index[$ukey] as $item)
				{
					if ($noiconv)
					{
						$ret[$key][] = $values[$item]["value"];
					}
					else
					{
						$ret[$key][] =DUtil_Convert::UTF8toGBK($values[$item]["value"]);
					}
				}
			}
		}

		xml_parser_free($xml_parser);
		return $ret;
	}

	// 同时获取节点属性
	static function xml2arr_attr($xml, $keys)
	{
		$xml_parser = xml_parser_create();
		if (0 === xml_parse_into_struct($xml_parser, $xml, $values, $index))
		{
			xml_parser_free($xml_parser);
			return false;
		}

		$ret = array();
		foreach ($keys as $key)
		{
			$ukey = strtoupper($key);
			if (is_array($index[$ukey]))
			{
				foreach ($index[$ukey] as $item)
				{
					$ret[$key][] = array(
						"value" =>DUtil_Convert::UTF8toGBK($values[$item]["value"]),
						"attributes" => $values[$item]["attributes"],
					);
				}
			}
		}
		xml_parser_free($xml_parser);

		return $ret;
	}

	static function obj2xml($data)
	{
		$str = "";
		foreach ($data as $name => $value)
		{
			if (is_numeric($name))
			{
				$name = "item";
			}
			if (is_array($value))
			{
				$str .= "<" . $name . ">" . self::obj2xml($value) . "</" . $name . ">";
			}
			else
			{
				settype($value, "string");
				$str .= "<" . $name . ">" . htmlspecialchars(DUtil_Convert::GBKtoUTF8($value)) . "</" . $name . ">";
			}
		}
		return $str;
	}

	static function obj2xml_gb($data)
	{
		$str = "";
		foreach ($data as $name => $value)
		{
			if (is_numeric($name))
			{
				$name = "item";
			}
			if (is_array($value))
			{
				$str .= "<" . $name . ">" . self::obj2xml_gb($value) . "</" . $name . ">";
			}
			else
			{
				settype($value, "string");
				$str .= "<" . $name . ">" . htmlspecialchars($value) . "</" . $name . ">";
			}
		}
		return $str;
	}

}

?>