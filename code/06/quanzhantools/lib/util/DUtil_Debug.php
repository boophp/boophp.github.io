<?php

/**
 * Debug
 *
 * @package debug
 * @author likang
 */


class DUtil_Debug
{
	private static $count = 0;
	/**
	 * 输出调试信息.
	 *
	 * @copyright kaixin001
	 * @param mixed $arr 输出对象
	 * @param string $title 标题提示信息
	 * @param int $T 输出类型 0 print_r 1 var_dump 2 var_export
	 * @example CDebug::print_r($arr,'$arr')
	 */
	public static function print_r( $arr, $title = '', $T = 0 )
	{ 
		$T = intval( $T );
		self::$count++;
		if ( self::$count == 1 )
		{

			?>
<style type="text/css">
<!--
*{
	margin:0px;
	padding:0px;
}
.m_fileldset {
	margin: 0px;
	padding: 2px;/*background-color: #06c;*/
	border: 1px dashed #09c;
	word-break:break-all;
	overflow:auto;
}
.m_legend {
	background-color: #06c;
	margin: 5px;
	padding: 2px;
	border: 1px solid #fff;
	color: #ffe;
	font-weight: bold;
	font-size:12px;
}
.m_button {
	border:1px solid #f96;
	background-color: #ffc;
}
.m_pre {
	text-align:left;
	font-size:12px;
}
-->
</style>
<script>
var m_sign = true;
function m_toggle() {
	var cs = document.getElementsByTagName("pre");
	var r = new Array();
	for(var i = 0;i<cs.length;i++)
	{
		var e = cs[i];
		if("m_pre" == e.className)
		{
			e.style.display = (m_sign == false ? "block" : "none");
			r.push( e);
		}
	}

	var cs = document.getElementsByTagName("button");

	for(var i = 0;i<cs.length;i++)
	{
		var e = cs[i];
		if("m_button" == e.className)
		{
			e.innerHTML = (m_sign == false ? "-" : "+");
		}
	}
	m_sign = !m_sign;
}
</script>
<button onclick="m_toggle()">Expand/Collapse All</button>
<?php
		}
		$temp_name = substr( md5( microtime() . serialize( $arr ) . $title . $T ), 0, 3 );

		?>
<fieldset class="m_fileldset" >
<legend class="m_legend">
<label style="cursor:pointer">
<?php echo $title; ?>
<?php
		if ( $arr )
		{

			?>
<button class="m_button" onclick="
var target = document.getElementById('<?php echo $temp_name;?>');
if (target.style.display != 'none' )
{
  target.style.display = 'none';
  this.innerHTML='+';
}
else
{
  target.style.display = 'block';
  this.innerHTML='-';
}">-</button>
</label>
<?php
		}

		?>
</legend>
<?php

		if ( $arr )
		{

			?>
<pre id="<?php echo $temp_name;?>" class="m_pre"><?php
			if ( 0 == $T )
			{
				self::_print_r( $arr );
			}
			else if ( 1 == $T )
			{
				self::_var_dump( $arr );
			}
			else if ( 2 == $T )
			{
				self::_var_export( $arr );
			}

			?>
</pre>
<?php
		}

		?>
</fieldset>
<?php
	}
	private static function _print_r( $arr )
	{
		echo htmlspecialchars( print_r ( $arr, true ) ) ;
	}
	private static function _var_dump( $arr )
	{
		var_dump( $arr );
	}
	private static function _var_export( $arr )
	{
		echo htmlspecialchars( var_export ( $arr, true ) ) ;
	}



	public static function pr( $arr, $title = '', $T = 0 ) {
		self::print_r($arr, $title, $T);
	}

	public static function pre( $arr, $title = '', $T = 0 ) {
		self::print_r($arr, $title, $T);
		exit;
	}

	
        public static function doLog($var, $title = '', $nameSpace = 'default') {
        	
		if (!defined('LOG_DIR')) {
			define('LOG_DIR', ROOT_DIR. '/log/');
		}
		
		global $glog;
		$glog->addFileLog($nameSpace, sprintf("T[%s]: %s", $title, $var));
        	
        }

        public static function doMyLog($uid, $var, $title = '', $nameSpace = 'default') {
        	
		$arrList	= array(
					11483	=> 1,
					);
					
		if (isset($arrList[$uid])) {
			self::doLog($var, $title, $nameSpace);
		}
        	
	}

        public static function myfpr2($uid, $var, $title = '', $nameSpace = 'default') {
        	self::myfpr($uid, $var, $title, $nameSpace);
	}

        public static function myfpr($uid, $var, $title = '', $nameSpace = 'default') {
		// 83965253
		// 87894864
		$arrList	= array(
					83965253	=> 1,
					87894864	=> 2,
					76981885	=> 2,	// chenhong
					132578		=> 2,	// leakon@hotmail.com
					);
		if (isset($arrList[$uid])) {

			self::fpr($var, $title, $nameSpace);

		}
	}

	protected static	$arrPrConf	= array();
	/**
	 * 把变量用 print_r 输出到文件中，避免干扰到页面正常输出
	 *
	 *
	 */
	public static function fpr($var, $title = '', $nameSpace = 'default') {

		ob_start();

		echo	sprintf("\n======== %s ========\n", $title);

		print_r($var);

		echo	sprintf("\n-------- -------- -------- --------\n");

		$content	= ob_get_contents();

		ob_end_clean();

		$int	= 0;

		if (strlen($content)) {

			$strFileName	= dirname(__FILE__) . '/../multilog/fpr/fpr.' . $nameSpace;

			if (isset(self::$arrPrConf['pr_init']) && 1 === self::$arrPrConf['pr_init']) {

				$int	= file_put_contents($strFileName, $content, FILE_APPEND);

			} else {
				
				if (file_exists($strFileName)) {
					unlink($strFileName);
				}
				
				$int	= file_put_contents($strFileName, $content);

				self::$arrPrConf['pr_init']	= 1;
				
			}

		}

		return	$int;

	}

	public static function pr8( $arr, $title = '', $T = 0 ) {
		$arr	= DUtil_Base::toUTF8($arr);
	 
		self::print_r($arr, $title, $T);
	}

	public static function pre8( $arr, $title = '', $T = 0 ) {
		$arr	= DUtil_Base::toUTF8($arr);
		self::print_r($arr, $title, $T);
		exit;
	}

	public static function toUtf8Deep($mixed) {

		$ret		= $mixed;

		if (is_array($mixed)) {

			foreach ($mixed as $key => $val) {
				$ret[$key]	= self::toUtf8Deep($val);
			}

		} else {

			$ret	= iconv("GB18030", "UTF-8//IGNORE", $ret);
		}

		return	$ret;

	}

    public static function Log($data)
    {
        $fd = fopen(ROOT_DIR."/log/debug.filelog", "a+");
        if (is_array($data)) {
            $data = json_encode($data);
        }
        fwrite($fd, $data . "\n");
        fclose($fd);
    }
}
?>