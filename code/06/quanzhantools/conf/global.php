<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */
/**
 * 全局控制文件
 *
 * @package
 * @subpackage
 */
define("DS", DIRECTORY_SEPARATOR);
if (!defined('ROOT_DIR'))
{
	define("ROOT_DIR", dirname(dirname(__FILE__)));
}

include_once("common.php");
?>
