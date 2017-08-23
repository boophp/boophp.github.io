<?php
/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */
/**
 * 生成数据库配置
 * 分表配置
 *
 * @package config
 * @author cyber4cn@gmail.com
 */
/**
 *   示例
 * global $table_map;
*  $table_map = array(
*  );
 */

/**
  * 服务器映射表	
  */
global $server_map;
$server_map = array(
	1=>array(
		"sid"=>1, 
		"master_sid"=>0, 
		"host"=>"10.131.170.167", 
		"port"=>3306, 
		"user"=>"quanzhan", 
		"passwd"=>"y7hh8yh", 
		"active"=>1, 
		"backup"=>0, 
		"remark"=>"", 
		"slaves"=>array(
		0=>array(
				"sid"=>2, 
				"master_sid"=>1, 
				"host"=>"10.131.172.145", 
				"port"=>3306, 
				"user"=>"quanzhan", 
				"passwd"=>"y7hh8yh", 
				"active"=>1, 
				"backup"=>0, 
				"remark"=>"", 
		) ,
		1=>array(
				"sid"=>3, 
				"master_sid"=>1, 
				"host"=>"10.131.172.146", 
				"port"=>3306, 
				"user"=>"quanzhan", 
				"passwd"=>"y7hh8yh", 
				"active"=>1, 
				"backup"=>0, 
				"remark"=>"", 
		) ,
		) ,
	) ,
	4=>array(
		"sid"=>4, 
		"master_sid"=>0, 
		"host"=>"10.131.167.12", 
		"port"=>3306, 
		"user"=>"quanzhan", 
		"passwd"=>"y7hh8yh", 
		"active"=>1, 
		"backup"=>0, 
		"remark"=>"", 
		"slaves"=>array(
		0=>array(
				"sid"=>5, 
				"master_sid"=>4, 
				"host"=>"10.131.167.13", 
				"port"=>3306, 
				"user"=>"quanzhan", 
				"passwd"=>"y7hh8yh", 
				"active"=>1, 
				"backup"=>0, 
				"remark"=>"", 
		) ,
		) ,
	) ,

);
/**
  * 表前缀配置映射表
  */
global $kind_map;
$kind_map = array(
	"qz_cart"=>array(
		"kind"=>"qz_cart", 
		"table_num"=>4, 
		"table_prefix"=>"qz_cart", 
		"id_field"=>"", 
		"remark"=>"", 
	) ,
	"qz_category"=>array(
		"kind"=>"qz_category", 
		"table_num"=>1, 
		"table_prefix"=>"qz_category", 
		"id_field"=>"", 
		"remark"=>"", 
	) ,
	"qz_goods"=>array(
		"kind"=>"qz_goods", 
		"table_num"=>4, 
		"table_prefix"=>"qz_goods", 
		"id_field"=>"", 
		"remark"=>"", 
	) ,
	"qz_goods_ex"=>array(
		"kind"=>"qz_goods_ex", 
		"table_num"=>4, 
		"table_prefix"=>"qz_goods_ex", 
		"id_field"=>"", 
		"remark"=>"", 
	) ,
	"qz_user"=>array(
		"kind"=>"qz_user", 
		"table_num"=>4, 
		"table_prefix"=>"qz_user", 
		"id_field"=>"", 
		"remark"=>"", 
	) ,

);
/**
  * 表配置映射表
  */
global $table_map;
$table_map = array(
	"qz_cart"=>array(
		0=>array(
				"sid"=>1, 
				"no"=>0, 
				"db_name"=>"quanzhan", 
		) ,
		1=>array(
				"sid"=>4, 
				"no"=>1, 
				"db_name"=>"quanzhan", 
		) ,
		2=>array(
				"sid"=>1, 
				"no"=>2, 
				"db_name"=>"quanzhan", 
		) ,
		3=>array(
				"sid"=>4, 
				"no"=>3, 
				"db_name"=>"quanzhan", 
		) ,
	) ,
	"qz_category"=>array(
		0=>array(
				"sid"=>1, 
				"no"=>0, 
				"db_name"=>"quanzhan", 
		) ,
	) ,
	"qz_goods"=>array(
		0=>array(
				"sid"=>1, 
				"no"=>0, 
				"db_name"=>"quanzhan", 
		) ,
		1=>array(
				"sid"=>4, 
				"no"=>1, 
				"db_name"=>"quanzhan", 
		) ,
		2=>array(
				"sid"=>1, 
				"no"=>2, 
				"db_name"=>"quanzhan", 
		) ,
		3=>array(
				"sid"=>4, 
				"no"=>3, 
				"db_name"=>"quanzhan", 
		) ,
	) ,
	"qz_goods_ex"=>array(
		0=>array(
				"sid"=>1, 
				"no"=>0, 
				"db_name"=>"quanzhan", 
		) ,
		1=>array(
				"sid"=>4, 
				"no"=>1, 
				"db_name"=>"quanzhan", 
		) ,
		2=>array(
				"sid"=>1, 
				"no"=>2, 
				"db_name"=>"quanzhan", 
		) ,
		3=>array(
				"sid"=>4, 
				"no"=>3, 
				"db_name"=>"quanzhan", 
		) ,
	) ,
	"qz_user"=>array(
		0=>array(
				"sid"=>1, 
				"no"=>0, 
				"db_name"=>"quanzhan", 
		) ,
		1=>array(
				"sid"=>4, 
				"no"=>1, 
				"db_name"=>"quanzhan", 
		) ,
		2=>array(
				"sid"=>1, 
				"no"=>2, 
				"db_name"=>"quanzhan", 
		) ,
		3=>array(
				"sid"=>4, 
				"no"=>3, 
				"db_name"=>"quanzhan", 
		) ,
	) ,

);
