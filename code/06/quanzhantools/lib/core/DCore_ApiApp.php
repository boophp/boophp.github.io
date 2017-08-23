<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DCore_ApiApp extends DCore_BaseApp
{
	/**
	 *  JSON 格式
	 */
	const OUTPUT_FORMAT_JSON = "text/json";
	/**
	 *  XML 格式
	 */
	const OUTPUT_FORMAT_XML = "text/xml";

	static $fmt = self::OUTPUT_FORMAT_JSON;
	static $output = array();
	protected $uid = 0;
	
	public function __construct($format= self::OUTPUT_FORMAT_JSON)
	{
		self::$fmt = $format;
	}

	protected function getParam($para, $default="", $type = TYPE_STR)
	{
		if (isset($_REQUEST[$para]))
		{
			return DCore_Input::clean("r", $para, $type);
		}
		return $default;
	}
	
	protected function outputResult()
	{
		switch (self::$fmt)
		{
			case self::OUTPUT_FORMAT_JSON:
				echo json_encode($this->output);
				break;
			case self::OUTPUT_FORMAT_XML:
				echo DUtil_XmlFunc::obj2xml($this->output);
				break;
		}
	}
	
	public function outputPage()
	{
		$this->outputResult();
		exit;
	}
	
	public function run()
	{
		try
		{
			$this->getPara();

			$this->checkPara();

			$this->checkAuth();

			$this->main();

			$this->outputPage();

			$this->exitApp();
		}
		catch (Exception $ex)
		{
			$this->showError($ex->getMessage(), $ex->getCode());
		}
	}
	
	protected function checkAuth()
	{
		$uid = $this->getParam('uid', 0, TYPE_UINT);
		$token = $this->getParam('token', '', TYPE_STR);
		$time = $this->getParam('time', '', TYPE_STR);
		
		if ($token == md5($uid . $time . DApi_Config::API_SECRET))
		{
			$this->uid = $uid;
		}
		else
		{
			//抛个异常出来。
		}
	}
	
	public function showError($msg, $code)
	{
		$this->output = array(
			"code"=> $code,
			"msg" => $msg,
		);
		$this->outputResult();
		exit;
	}

}

?>
