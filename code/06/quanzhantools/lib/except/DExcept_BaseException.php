<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DExcept_BaseException extends Exception
{

	public function __construct($code, $msg="")
	{
		if (!$msg)
		{
			$msg = DExcept_Const::$msg_map[$code];
		}
//       / print_r(debug_backtrace());
	//DUtil_Debug::pr(debug_backtrace());
       // exit;
		parent::__construct($msg, $code);
	}

}

?>