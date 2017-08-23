<?php

/**
 *
 *
 * @package
 * @subpackage
 */



class DCore_BackApp extends DCore_ConsoleApp
{
 	protected function checkAuth()
	{
		
	}

	//由子类去实现
	protected function getParameter()
	{
		
	}

	//由子类去实现
	protected function checkParameter()
	{
		
	}

	protected function getPara()
	{
		$this->_date = $this->getParam("date", date("Ymd", time() - 86400));
		$this->getParameter();
	}

	protected function checkPara()
	{
		$this->checkParameter();
		if ($this->_date == "")
		{
			DAdmin_Utils::printLog("_date not found !");
			exit;
		}
	}

}

?>
