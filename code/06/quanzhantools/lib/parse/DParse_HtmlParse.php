<?php
/**
 * HTML解析类
 * @package common
 * @author
 */
class DParse_HtmlParse
{
	public static $allpairs = array(
		'table' => '', 'tbody' => '', 'th' => '', 'tr' => '', 'td' => '',
		'b' => '', 'strike' => '', 'i' => '', 'u' => '', 'font' => '',
		'sub' => '', 'sup' => '', 'em' => '', 's' => '', 'strong' => '',
		'ol' => '', 'ul' => '', 'blockquote' => '', 'a' => '',
		'p' => '', 'div' => '', 'center' => '', 'span' => '',
	);
	public static $allsingle = array(
		'br' => '', 'hr' => '', 'li' => '', 'img' => '', 'embed' => '',
	);

	public static $checkProtocolsAttr = array(
		'src' => '', 'href' => '',
	);
	public static $allowProtocols = array(
		'mms' => '', 'http' => '', 'https' => '', 'ftp' => '',
	);

	# 必须关闭的标签
	protected $htmlpairs = array();

	#独立的标记
	protected $htmlsingle = array();

	//所有标签
	protected $htmlelements = array();

	//过滤级别	// ""|"app"|"msg"|"audit"
	protected $strict = "";

	static function forbidScript($text)
	{
		$text = str_replace("\r", "", $text);
		$text = iconv(DB_CHARSET, SYS_CHARSET."//IGNORE", $text);
		$text = iconv(SYS_CHARSET, DB_CHARSET."//IGNORE", $text);
		return preg_replace("/script/i", " script ", $text);
	}

	static function isPair($tagname)
	{
			return array_key_exists(strtolower($tagname), self::$allpairs);
	}

	static function isSingle($tagname)
	{
			return array_key_exists(strtolower($tagname), self::$allsingle);
	}

	function parse( $text , $strict = "")
	{
		$this->strict = $strict;
		if ($this->strict == "app")
		{
			$this->htmlpairs = array(
				'div' => '', 'span' => '', 'p' => '', 'a' => '',
			);
			$this->htmlsingle = array(
				'br' => '', 'img' => '',
			);
		}
		else if ($this->strict == "msg")
		{
			$this->htmlpairs = array(
				'p' => '',
			);
			$this->htmlsingle = array(
				'br' => '', 'img' => '',
			);
			$webkit_fix = array(
				'/<div\s*><br><\/div\s*>/' => '<br />',
				'/<div\s*>/' => '<br />',
				'/<\/div\s*>/' => '',
				);
			$text = preg_replace(array_keys($webkit_fix), array_values($webkit_fix), $text);
		}
		else if ($this->strict == "audit")
		{
			$this->htmlpairs = array(
				'p' => '', 'a' => '', 'b' => '',
			);
			$this->htmlsingle = array(
				'br' => '', 'img' => '', 'embed' => '',
			);
		}
		else if ($this->strict == "repaste")
		{
			$this->htmlpairs = array(
				'font' => '', 'span' => '',
				'p' => '', 'center' => '',
			);
			$this->htmlsingle = array(
				'br' => '', 'img' => '',
			);
		}
		else
		{
			$this->htmlpairs = self::$allpairs;
			$this->htmlsingle = self::$allsingle;
		}
		$this->htmlelements = array_merge( $this->htmlsingle, $this->htmlpairs );

		$text = $this->removeHTMLtags($text);
		$text = $this->forbidScript($text);

		$fixtags = array(
			'/\\t/' => ' ',
			'/<hr *>/i' => '<hr />',
			'/<br *>/i' => '<br />',
			'/<center *>/i' => '<div style="text-align:center">',
			'/<\\/center *>/i' => '</div>',
			'/<embed /i' => '<embed type="application/x-shockwave-flash" allowscriptaccess="never" ',
		);
		if ($this->strict != "app")
		{
			$fixtags['/<a /i'] = '<a target="_blank" ';
		}
		$text = preg_replace( array_keys($fixtags), array_values($fixtags), $text );

		return $text;
	}

	/**
	 * 返回允许的HTML参数
	 * @access private
	 */
	function getHTMLattrs ($name)
	{
		$name = strtolower($name);

		if ($this->strict == "app")
		{
			switch ($name)
			{
			case 'img':
				$htmlattrs = array(
					'title' => '', 'src' => '', 'width' => '', 'height' => '', 'align' => '',
				);
				break;
			default:
				$htmlattrs = array(
					'href' => '', 'target' => '', /* For A */
					'style' => '', 'class' => '', /* For DIV */
					'wmode' => '',
				);
			}
		}
		else if ($this->strict == "msg")
		{
			switch ($name)
			{
			case 'img':
				$htmlattrs = array(
					'title' => '', 'src' => '', 'width' => '', 'height' => '', 'align' => '',
				);
				break;
			default:
				$htmlattrs = array();
			}
		}
		else if ($this->strict == "audit")
		{
			switch ($name)
			{
			case 'img':
				$htmlattrs = array(
					'width' => '', 'height' => '', 'src' => '', 'align' => '',
				);
				break;
			case 'embed':
				$htmlattrs = array(
					'src' => '', 'width' => '', 'height' => '',
					'loop' => '', 'autostart' => '', 'wmode' => '',
				);
				break;
			default:
				$htmlattrs = array(
					'width' => '', 'height' => '',
					'href' => '', 'target' => '',
					'src' => '',
				);
			}
		}
		else if ($this->strict == "repaste")
		{
			switch ($name)
			{
				case 'img':
					$htmlattrs = array(
						'title' => '', 'src' => '', 'width' => '', 'height' => '', 'align' => '',
					);
					break;
				case 'font':
					$htmlattrs = array(
						'color' => '', 'size' => '', 'face' => '',
					);
					break;
				case 'span':
					$htmlattrs = array(
						'style' => '',
					);
					break;
				default:
					$htmlattrs = array();
			}
		}
		else
		{
			switch ($name)
			{
			case 'img':
				$htmlattrs = array(
					'title' => '', 'align' => '', 'border' => '', 'width' => '', 'height' => '',
					'valign' => '', 'src' => '', 'hspace' => '', 'vspace' => '',
				);
				break;
			case 'table':
				$htmlattrs = array(
					'align' => '', 'border' => '', 'cellspacing' => '',
					'cellpadding' => '', 'valign' => '',
				);
				break;
			case 'embed':
				$htmlattrs = array(
					'src' => '', 'width' => '', 'height' => '',
					'loop' => '', 'autostart' => '', 'wmode' => '',
					'quality' => '', 'scale' => '', 'bgcolor' => '',
					'name' => '', 'allowfullscreen' => '', 'pluginspage' => '', 'align' => '',
					'flashvars' => '',
				);
				break;
			default:
				$htmlattrs = array(
					'title' => '', 'align' => '', 'valign' => '', 'width' => '', 'height' => '',
					'bgcolor' => '', 'clear' => '', /* BR */
					'noshade' => '', /* HR */
					'size' => '', 'face' => '', 'color' => '', /* FONT */
					'border' => '', 'cellspacing' => '', 'cellpadding' => '', 'span' => '', /* Tables */
					'href' => '', 'target' => '', /* For A */
					'src' => '', 'hspace' => '', 'vspace' => '', /* For IMAGE */
					'style' => '',
				);
			}
		}
		return $htmlattrs ;
	}

	/**
	 * 清理HTML, 删除危险的标记和变量, 并且清理HTML注释
	 * @access private
	 * @param string $text
	 * @return string
	 */
	function removeHTMLtags( $text )
	{
		# 删除所有HTML注释
		$text = $this->removeHTMLcomments( $text );

		$bits = explode( '<', $text );
		$text = array_shift( $bits );

		foreach ( $bits as $x )
		{
			preg_match( '/^(\\/?)(\\w+)([^>]*?)(\\/{0,1}>)([^<]*)$/', $x, $regs );
			@list( $qbar, $slash, $t, $params, $brace, $rest ) = $regs;
			//echo "$qbar, $slash, $t, $params, $brace, $rest \n";
			if ( array_key_exists( $t = strtolower( $t ), $this->htmlelements ) ) {
				$newparams = $this->fixTagAttributes($params, $t);
				$rest = str_replace( '>', '&gt;', $rest );
				$text .= "<$slash$t $newparams$brace$rest";
			} else {
				//$text .= '&lt;' . str_replace( '>', '&gt;', $x);
				$text .= $rest;
			}
		}
		return $text;
	}

	/**
	 * 删除'<!--', '-->', 之间的任何标记.
	 * @access private
	 */
	static function removeHTMLcomments( $text )
	{
		while (($start = strpos($text, '<!--')) !== false)
		{
			$end = strpos($text, '-->', $start + 4);
			if ($end === false) {
				$text = substr($text, 0, $start);
				break;
			}
			$text = substr($text, 0, $start)." ".substr($text, $end+3);
		}
		return $text;
	}

	/**
	 * 删除不被允许的标记
	 * @access private
	 */
	function fixTagAttributes ( $t, $ext)
	{
		if ( trim ( $t ) == '' ) return '' ;
		if( !preg_match_all('/(\\w+)(\\s*=\\s*([^\\s\"\'>]+|\"[^\">]*\"|\'[^\'>]*\'))?(?=\\s|$)/', $t, $matches, PREG_SET_ORDER ) ) {
			return '';
		}
		$out = '';
		$embedisvalid = true;
		foreach( $matches as $set ) {
			$htmlattrs = $this->getHTMLattrs($ext) ;
			$set[1] = strtolower( $set[1] );

			if( array_key_exists( $set[1] , $htmlattrs ) ) {
				if ( array_key_exists( $set[1] , self::$checkProtocolsAttr ) ) {
					$link = trim($set[3]);
					$link = trim($link, "\"'");
					$link .= "://";
					list($protocol, $link) = explode("://", $link, 2);
					if ( strlen( $link ) == 0 && strlen( $protocol ) && $protocol[0] == "/" ) {
						$set[3] = '"http://'.getenv("HTTP_HOST").$protocol.'"';
					}
					else {
						$protocol = strtolower ( $protocol );
						if ( !array_key_exists( $protocol , self::$allowProtocols ) ) {
							continue;
						}
					}
				}

				if ($ext == "embed" && $set[1] == "src" && (CUserBookmark::checkSwfUrl($set[3])== "" && CUserBookmark::getSrc($set[3]) == ""))	//$set[3]不在白名单
				{
					$embedisvalid = false;
				}

				if (strtolower($set[1]) == "style") {
					//允许的style白名单
					$reg_arr = array(
						'background-color: ?#\\w{3,6}',
						'color: ?rgb\(\\d{1,3}, ?\\d{1,3}, ?\\d{1,3}\)',
						'background-color: ?rgb\(\\d{1,3}, ?\\d{1,3}, ?\\d{1,3}\)',
						'\\*?color: ?#\\w{3,6}',
						'text-align: ?(left|center|right)',
						'font-weight: ?\\w*',
						'font-style: ?\\w*',
						'font-size: ?\\w*',
						'font-family: ?[^\\"]*',
						'text-decoration: ?\\w*',
						'display: ?.*',
						'padding: ?.*',
						'padding-right: ?\\w*',
						'padding-left: ?\\w*',
						'padding-bottom: ?\\w*',
						'padding-top: ?\\w*',
						'width: ?\\w*',
						'line-height: ?[\\w\.\\s]*',
						'border-bottom: ?.*',
						'margin: ?[\\w\\s]*',
						'filter: ?glow\\([^\\"]*\\)',
						'zoom: ?1',
					);

					//白名单内的任意组合
					$regs = '/^"?((';
					for ($i=0; $i<sizeof($reg_arr); $i++) {
						if ($i==0)
							$regs .= $reg_arr[$i];
						else
							$regs .= '|'.$reg_arr[$i];
					}
					$regs .= ');? ?)*"?$/i';

					//不匹配白名单内的任意组合则认为不合法
					if (!preg_match($regs, strtolower($set[3]))) {
						continue;
					}
				}

				$out .= ' ' . $set[1];
				if( isset( $set[3] ) ) {
					if( $set[3] == "''" ) {
						$out .= '=""';
					} else {
						$out .= '=' . $set[3];
					}
				}
			}
		}

		if ($ext == "embed")
		{
			if ($embedisvalid)
			{
				$out = 'allownetworking="internal"'.$out;
			}
			else
			{
				$out = 'allownetworking="none"'.$out;
			}
		}

		return trim( $out );
	}

	//下面的函数用来进行html分析

	static function findFirstLt($content, $start, &$gtpos)
	{
		$start = strpos($content, "<", $start);
		if ($start === false)
		{
			return false;
		}
		$pos1 = strpos($content, "<", $start + 1);
		$pos2 = strpos($content, ">", $start + 1);
		if (false === $pos2)
		{
			return false;
		}
		else if (false === $pos1 || $pos2 < $pos1)
		{
			$gtpos = $pos2;
			return $start;
		}
		else
		{
			return CHtmlParse::findFirstLt($content, $start + 1, $gtpos);
		}
	}

	static function findLastGt($content, &$ltpos)
	{
		$start = strrpos($content, ">");
		if ($start === false)
		{
			return false;
		}
		$content = substr($content, 0, $start);
		$pos1 = strrpos($content, ">");
		$pos2 = strrpos($content, "<");
		if (false === $pos2)
		{
			return false;
		}
		else if (false === $pos1 || $pos2 > $pos1)
		{
			$ltpos = $pos2;
			return $start;
		}
		else
		{
			return CHtmlParse::findLastGt($content, $ltpos);
		}
	}

	static function html2hbt($content)
	{
		$start = CHtmlParse::findFirstLt($content, 0, $gtpos);
		$end = CHtmlParse::findLastGt($content, $ltpos);
		if ($start === false || $end === false || $end < $start)
		{
			return array("head" => trim($content), "t1" => "", "body" => "", "t2" => "", "tail" => "");
		}
		if ($ltpos == $start)
		{
			return array("head" => trim(substr($content, 0, $start)), "t1" => substr($content, $start, $gtpos - $start + 1), "body" => "", "t2" => "", "tail" => trim(substr($content, $end + 1)));
		}
		$body = trim(substr($content, $gtpos + 1, $ltpos - $gtpos - 1));
		if (0 == strlen($body))
		{
			return array("head" => trim(substr($content, 0, $start)), "t1" => substr($content, $start, $gtpos - $start + 1), "body" => $body, "t2" => substr($content, $ltpos, $end - $ltpos + 1), "tail" => trim(substr($content, $end + 1)));
		}
		return array("head" => trim(substr($content, 0, $start)), "t1" => substr($content, $start, $gtpos - $start + 1), "body" => CHtmlParse::html2hbt($body), "t2" => substr($content, $ltpos, $end - $ltpos + 1), "tail" => trim(substr($content, $end + 1)));
	}

	static function getTagName($t)
	{
		$t = trim($t, "<>");
		$t = explode(" ", $t, 2);
		$t = strtolower($t[0]);
		if ($t[0] == "/")
		{
			return array(substr($t, 1), false);
		}
		return array($t, true);
	}

	static function tag2tree($tag, &$tree, &$taglist)
	{
		list($tagname, $begin) = CHtmlParse::getTagName($tag);
		if (CHtmlParse::isPair($tagname))
		{
			if ($begin)
			{
				$taglist[] = array(
					"type" => "pair",
					"tagname" => $tagname,
					"begin" => $tag,
					"end" => "</".$tagname.">",
					"content" => array(),
				);
			}
			else
			{
				$len = count($taglist);
				for ($i=$len-1; $i>=0; $i--)
				{
					if ($taglist[$i]["tagname"] == $tagname)
					{
						break;
					}
				}
				if ($i >= 0)
				{
					for ($ii=$len-1; $ii>$i; $ii--)
					{
						$taglist[$ii-1]["content"][] = $taglist[$ii];
						array_pop($taglist);
					}
					$taglist[$i]["end"] = $tag;
					if ($i)
					{
						$taglist[$i-1]["content"][] = $taglist[$i];
					}
					else
					{
						$tree[] = $taglist[$i];
					}
					array_pop($taglist);
				}
			}
		}
		else if (CHtmlParse::isSingle($tagname))
		{
			if ($begin)
			{
				if ($taglist)
				{
					$len = count($taglist);
					$taglist[$len-1]["content"][] = array(
						"type" => "single",
						"tagname" => $tagname,
						"begin" => $tag,
						"end" => "",
						"content" => "",
					);
				}
				else
				{
					$tree[] = array(
						"type" => "single",
						"tagname" => $tagname,
						"begin" => $tag,
						"end" => "",
						"content" => "",
					);
				}
			}
		}
	}

	static function _html2tree($hbt, &$tree, &$taglist)
	{
		if (strlen($hbt["head"]))
		{
			$len = count($taglist);
			if ($len)
			{
				$taglist[$len-1]["content"][] = array(
					"type" => "text",
					"tagname" => "",
					"begin" => "",
					"end" => "",
					"content" => $hbt["head"],
				);
			}
			else
			{
				$tree[] = array(
					"type" => "text",
					"tagname" => "",
					"begin" => "",
					"end" => "",
					"content" => $hbt["head"],
				);
			}
		}
		if (strlen($hbt["t1"]))
		{
			CHtmlParse::tag2tree($hbt["t1"], $tree, $taglist);
		}
		if ($hbt["body"])
		{
			CHtmlParse::_html2tree($hbt["body"], $tree, $taglist);
		}
		if (strlen($hbt["t2"]))
		{
			CHtmlParse::tag2tree($hbt["t2"], $tree, $taglist);
		}
		if (strlen($hbt["tail"]))
		{
			$len = count($taglist);
			if ($len)
			{
				$taglist[$len-1]["content"][] = array(
					"type" => "text",
					"tagname" => "",
					"begin" => "",
					"end" => "",
					"content" => $hbt["tail"],
				);
			}
			else
			{
				$tree[] = array(
					"type" => "text",
					"tagname" => "",
					"begin" => "",
					"end" => "",
					"content" => $hbt["tail"],
				);
			}
		}
		return $hbt;
	}

	static function html2tree($content)
	{
		$hbt = CHtmlParse::html2hbt($content);

		$tree = array(
			"type" => "pair",
			"tagname" => "",
			"begin" => "",
			"end" => "",
			"content" => array(),
		);
		$taglist = array();
		CHtmlParse::_html2tree($hbt, $tree["content"], $taglist);
		$tree["content"] = array_merge($tree["content"], $taglist);

		return $tree;
	}

	static function pair2html($item, $length, $ext, &$taglist, &$html, &$txtlen, &$cut)
	{
		if ($txtlen >= $length)
		{
			return;
		}
		if (strlen($item["tagname"]))
		{
			$html .= $item["begin"];
			$len = count($taglist);
			$taglist[] = $item["tagname"];
		}
		foreach ($item["content"] as $v)
		{
			if ($v["type"] == "pair")
			{
				CHtmlParse::pair2html($v, $length, $ext, $taglist, $html, $txtlen, $cut);
			}
			else if ($v["type"] == "single")
			{
				CHtmlParse::single2html($v, $length, $ext, $taglist, $html, $txtlen, $cut);
			}
			else if ($v["type"] == "text")
			{
				CHtmlParse::text2html($v, $length, $ext, $taglist, $html, $txtlen, $cut);
			}
			if ($txtlen >= $length)
			{
				$cut = true;
				return;
			}
		}
		if (strlen($item["tagname"]))
		{
			$html .= $item["end"];
			array_pop($taglist);
		}
	}

	static function single2html($item, $length, $ext, &$taglist, &$html, &$txtlen, $cut)
	{
		$html .= $item["begin"];
	}

	static function text2html($item, $length, $ext, &$taglist, &$html, &$txtlen, $cut)
	{
		$html .= CStr::subString($item["content"], $length - $txtlen, $ext);
		$txtlen += strlen($item["content"]);
	}

	static function getAbstract($content, $length, $ext = "", $dropBadTags = false)
	{
		$len = strlen($content);
		$subcut = false;
		
		if($len > $length * 10)
		{
			$content = CStr::subString($content, $length*10);
			$subcut = true;
		}
		//丢掉最后一个被截断的html标签
		if ($dropBadTags)
		{
			if (strrpos($content, ">") && strrpos($content, ">") < strrpos($content, "<"))
			{
				$content = substr($content, 0, strrpos($content, "<"));
			}
		}

		$tree = CHtmlParse::html2tree($content);
		$html = "";
		$txtlen = 0;
		$taglist = array();
		$cut = false;
		CHtmlParse::pair2html($tree, $length, $ext, $taglist, $html, $txtlen, $cut);
		
		$len = count($taglist);
		for ($i=$len-1; $i>=0; $i--)
		{
			$html .= "</".$taglist[$i].">";
		}
		if($subcut)
		{
			$cut = true;
		}
		
		return array($html, $cut);
	}


	static function getCookies($header)
	{
		$cookies=array();
		$cookie="";
		$returnar=explode("\r\n",$header);
		$rcount = count($returnar);
		for($ind=0;$ind<$rcount;$ind++)
		{
			if(ereg("Set-Cookie:",$returnar[$ind]) || ereg("Cookies ",$returnar[$ind]))
			{
				$cookie=trim(str_replace("Set-Cookie:","",$returnar[$ind]));
				$cookie=explode(";",$cookie);
				$cookvalues = explode("=", $cookie[0]);
				if($cookvalues[1]== "")
				{
					continue;
				}
				$cookies[trim($cookie[0])]=trim($cookie[0]);
				/*$cookie=trim(str_replace("Set-Cookie:","",$returnar[$ind]));
				$cookie=explode(";",$cookie);
				foreach($cookie as $item)
				{
					$cookvalues = explode("=", $item);
					if($cookvalues[1]== "")
					{
						continue;
					}
					$cookies[trim($item)]=trim($item);
				}*/
			}
		}

		$cookie=array();
		foreach ($cookies as $key=>$value)
		{
			array_push($cookie,"$value");
		}
		$cookie=implode(";",$cookie);
		return $cookie;
	}
	//html分析


	// 处理尾部 &nbsp; 类似的 html
	protected static
		$strHtmlWordSearchString	= '|&nbsp;|&lg;|&gt;|&amp;|';

	/**
	 * 修复被错误截断的 html 代码 （目前仅处理 &nbsp; 类似的 html，其他格式可日后添加）
	 *
	 * @param string	$strContent	引用传递字符串，如果有需要则直接修改，否则什么也不做
	 * @param string	$strExt		截断后的扩展字符，应与原字符串默认的扩展字符相同
	 *
	 * add by Liulikang (2010-07-08)
	 */
	public static function fixTailHtml(&$strContent, $strExt = '...') {

		$intContLen	= strlen($strContent);

		$intExtLen	= strlen($strExt);

		// 获取尾部 10 个字符
		if (($intExtLen + 10) > $intContLen) {
			// 纯文本不到 10 个字节，则退出，什么也不做
			return;
		}
		$strTail	= substr($strContent, 0 - 10 - $intExtLen);

		// 获取尾部纯文本（删掉扩展字符）
		$strTail	= substr($strTail, 0, 0 - $intExtLen);

		// 寻找最后一个 "&"
		$intLastPos		= strrpos($strTail, '&');

		// 取最后一个可能被截断的 html
		$strPart		= substr($strTail, $intLastPos);

		// 能找到前半部分，不能完整匹配的，肯定是截断了的字符
		if (false !== strpos(self::$strHtmlWordSearchString, '|' . $strPart)
			&& false === strpos(self::$strHtmlWordSearchString, '|' . $strPart . '|'))
		{

			// 截断的字符长度 + 扩展字符长度
			$intEnd		= strlen($strPart) + $intExtLen;

			// 删掉截断的字符
			$strContent	= substr($strContent, 0, 0 - $intEnd) . $strExt;

		}

		// 否则什么也不做

	}




}

/*
if ($argc == 3)
{
	include_once("CStr.php");

	$content = file_get_contents($argv[2]);
	if ($argv[1] == "html2tree")
	{
		$ret = CHtmlParse::html2tree($content);
	}
	else if ($argv[1] == "getAbstract")
	{
		$ret = CHtmlParse::getAbstract($content, "500", "...");
	}
	var_dump($ret);
}
*/

?>