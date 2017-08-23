<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */
/**
 * 公共全局方法
 *
 * @package
 * @subpackage
 * @author
 */
ini_set("date.timezone", "Asia/Chongqing");

define("INCLUDE_PATH", ROOT_DIR . DS . "lib" . DS);
define("LIB_DIR", ROOT_DIR . DS . "lib" . DS);
define("HTDOCS_DIR", ROOT_DIR . DS . "htdocs" . DS);
define("LOG_DIR", ROOT_DIR . DS . "log" . DS);
define("DATA_DIR", ROOT_DIR . DS . "data" . DS);
 

require_once 'conf.php';
include_once(ROOT_DIR."/conf/limit/limitconf.php");
                                

                                

//http://my.cntv.cn/
//define("WEB_ROOT", "http://" . $host);
global $oLog;
global $arr_loginfo;

function __autoload($class_name)
{
	if ($class_name[0] == 'C')
	{
		include_once (INCLUDE_PATH . DS . $class_name . '.php');
	}
}

class HPAutoLoad
{

	public static function autoload($class_name)
	{
		// 【1】类名以字母 "D" 开头，意思是类定义文件的存放位置是以 Directory 划分的
		// 【2】pos 大于 1 ，意思是 "D" 之后、下划线 "_" 之前必须要有字符
		// 类名满足这 2 个条件，才使用本 autoload 函数
		if ('D' == $class_name[0] && 1 < ($pos = strpos($class_name, '_')))
		{

			// 目录名去掉 "D"，并转换为小写字母
			$dir = strtolower(substr($class_name, 1, $pos - 1));

			// 基础路径为 include/
			require_once(INCLUDE_PATH . DS . $dir . DS . $class_name . '.php');

			return true;
		}
		else if ('M' == $class_name[0] && 1 < ($pos = strpos($class_name, '_')))
		{
			$dir = strtolower(substr($class_name, 1, $pos - 1));
			$namearr = explode('_', $class_name);
			$classtype = strtolower($namearr[count($namearr) - 1]);
			$subdir = "model";
			switch ($classtype)
			{
				case "action":
					$subdir = "controller";
					break;
				case "view":
					$subdir = "view";
					break;
				default:
					$subdir = "model";
			}
			// var_dump(INCLUDE_PATH . '/' . $dir . '/' . $subdir . '/' . $class_name . '.php');
			require_once(INCLUDE_PATH . DS . $dir . DS . $subdir . DS . $class_name . '.php');
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function register()
	{

		// 新的注册函数，优先级更高
		spl_autoload_register(array('HPAutoLoad', 'autoload'));

		// 原有的 __autoload 优先级较低
		spl_autoload_register('__autoload');
	}

}

HPAutoLoad::register();

 include_once(INCLUDE_PATH.'/util/DUtil_Log.php');
                                

  function objectToArray($d) {  
        if (is_object($d)) {  
            $d = get_object_vars($d);  
        }  
   
        if (is_array($d)) {  
            return array_map(__FUNCTION__, $d);  
        }  
        else {  
            // Return array  
            return $d;  
        }  
    }  
      
     function arrayToObject($d) {  
        if (is_array($d)) {  
            return (object) array_map(__FUNCTION__, $d);  
        }  
        else {  
            return $d;  
        }  
    }  

?>
