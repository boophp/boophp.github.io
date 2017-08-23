<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * 应用程序基类
 *
 * @package
 * @subpackage
 */
class DCore_BaseApp
{

	protected function getPara()
	{
	}

	protected function checkPara()
	{

	}

	protected function main()
	{

	}
	
	protected function checkAuth()
	{
		
	}

	protected function exitApp()
	{


	}
	
	protected function outputPage()
	{


	}
	

	public function run()
	{
		
		$this->getPara();

		$this->checkPara();

		$this->main();
		
		$this->outputPage();

		$this->exitApp();
	}
	
	
}

?>
