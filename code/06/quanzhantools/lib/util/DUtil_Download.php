<?php
define('DOWNLOAD_FILE', 0);
define('DOWNLOAD_STR', 1);
define('DOWNLOAD_REDIRECT', 2);

class DUtil_Download {
	protected $data = null;
	protected $data_len = 0;
	protected $data_mod = 0;
	protected $data_type = DOWNLOAD_FILE;
	protected $use_seek = false;
	protected $sentsize = 0;
	protected $handler = array('auth' => null);
	protected $use_auth = false;
	protected $filename = null;
	protected $mime = null;
	protected $bufsize = 2048;
	protected $seek_start = 0;
	protected $seek_end = -1;
	protected $bandwidth = 0;
	protected $speed = 0;
	protected $seek_range = '';
	protected $content_null = false;

	function _auth()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) 
		{
			return false;
		}
		
		if (isset($this->handler['auth']) && function_exists($this->handler['auth']))
		{
			return $this->handler['auth']('auth' , $_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		}
		else
		{
			return true;
		}
	}
	
	function checkAuth()
	{
		if ($this->use_auth)
		{
			if (!$this->_auth())
			{
				header('WWW-Authenticate: Basic realm="Please enter your username and password"');
				header('HTTP/1.0 401 Unauthorized');
				header('status: 401 Unauthorized');
				exit;
			}
		}
		
		if ($this->content_null)
		{
			$this->header();
		}
	}
	
	function initialize()
	{
		if (!$this->filename) 
		{
			if ($this->data_type == DOWNLOAD_FILE)
			{
				$this->filename = basename($this->data);
			}
			else if ($this->data_type == DOWNLOAD_STR)
			{
				$this->filename = time();
			}
		}
		
		if (!$this->mime) 
		{
			$this->mime = "application/octet-stream";
		}

		$this->sentsize = 0;
		
		if ($this->data_type == DOWNLOAD_FILE)
		{
			$this->data_len = filesize($this->data);
		}
		else if ($this->data_type == DOWNLOAD_STR)
		{
			$this->data_len = strlen($this->data);
		}
		else if ($this->data_type == DOWNLOAD_REDIRECT)
		{
			$this->data_len = 0;
		}
		
		if (!$this->data_mod)
		{
			if ($this->data_type == DOWNLOAD_FILE)
			{
				$this->data_mod = filemtime($this->data);
				
			}
			else if ($this->data_type == DOWNLOAD_STR)
			{
				$this->data_mod = time();
			}
			else if ($this->data_type == DOWNLOAD_REDIRECT)
			{
				$this->data_mod = time();
			}
		}
		
		if ($this->seek_range)
		{
			$range = explode('-', $this->seek_range);

			if (isset($range[0]) && intval($range[0]) > 0)
			{
				$this->seek_start = intval($range[0]);
				$this->use_seek = true;
			}
			else
			{
				$this->seek_start = 0;
			}

			if (isset($range[1]) && intval($range[1]) > 0 && (intval($range[1]) < $this->data_len)) 
			{
				$this->seek_end = intval($range[1]);
				$this->use_seek = true;
			}
			else 
			{
				$this->seek_end = $this->data_len - 1;
			}

			if ($this->seek_start > ($this->data_len - 1)) 
			{
				$this->content_null = true;
				$this->header();
			}
		}
		else
		{
			$this->seek_start = 0;
			$this->seek_end = $this->data_len - 1;
		}
	}

	function header() 
	{
		header('Content-type: ' . $this->mime);
		header('Content-Disposition: attachment; filename="' . $this->filename . '"');
		header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T' , $this->data_mod));
		header('Content-Encoding: no-gzip');
		
		if ($this->content_null)
		{
			header("Content-Length: 0");
			exit;
		}
		else
		{
			$size = ($this->data_len)?$this->data_len:0;
			$seek_start = $this->seek_start;
			$seek_end = $this->seek_end;
		
			if ($this->use_seek)
			{
				header("HTTP/1.0 206 Partial Content");
				header("Status: 206 Partial Content");
				header('Accept-Ranges: bytes');
				header("Content-Range: bytes $seek_start-$seek_end/$size");
				header("Content-Length: " . ($seek_end - $seek_start + 1));
			}
			else
			{
				header("Content-Length: {$size}");
			}
		}
	}

	function download() 
	{
		$this->checkAuth();
		$this->initialize();

		try
		{
			$speed = $this->speed;
			$bufsize = $this->bufsize;
			$packet = 1;
			$this->bandwidth = 0;

			@ob_end_clean();
			@set_time_limit(0);

			if ($this->data_type == DOWNLOAD_FILE)
			{
				$res = fopen($this->data,'rb');
				if ($this->seek_start) 
				{
					fseek($res , $this->seek_start);
				}

				$size = $this->seek_end - $this->seek_start + 1;
								
				$this->header();

				while (!(connection_aborted() || connection_status() == 1) && $size > 0)
				{
					 if ($size < $bufsize)
					 {
						  echo fread($res , $size);
						  $this->bandwidth += $size;
						  $this->sentsize += $size;
					 }
					 else
					 {
						  echo fread($res , $bufsize);
						  $this->bandwidth += $bufsize;
						  $this->sentsize += $bufsize;
					 }

					 $size -= $bufsize;
					 flush();

					 if ($speed > 0 && ($this->bandwidth > $speed * $packet * 1024))
					 {
						  sleep(1);
						  $packet++;
					 }
				}
				
				fclose($res);
			}
			else if ($this->data_type == DOWNLOAD_STR)
			{
				$this->data = substr($this->data, $this->seek_start, $this->seek_end - $this->seek_start + 1);
				$size = strlen($this->data);

				$this->header();
				while (!connection_aborted() && $size > 0)
				{
					if ($size < $bufsize)
					{
						$this->bandwidth += $size;
						$this->sentsize += $size;
					}
					else
					{
						$this->bandwidth += $bufsize;
						$this->sentsize += $bufsize;
					}

					echo substr($this->data , 0 , $bufsize);
					$this->data = substr($this->data , $bufsize);

					$size -= $bufsize;
					flush();

					if ($speed > 0 && ($this->bandwidth > $speed * $packet * 1024))
					{
						sleep(1);
						$packet++;
					}
				}
			}
			else if ($this->data_type == DOWNLOAD_REDIRECT)
			{
				header('location: ' . $this->data);
			}

			if(($this->seek_end - $this->seek_start + 1) == $this->sentsize)
			{
				//下载完成
			}
			else
			{
				//下载终止
			}

			@set_time_limit(ini_get("max_execution_time"));
		}
		catch(Exception $e)
		{

		}
		
		return true;
	}
	
	//设置filename
	function setFilename($filename)
	{
		$this->filename = $filename;
	}
	
	//设置下载文件
	function setByFile($dir)
	{
		if (is_readable($dir) && is_file($dir))
		{
			$this->data = $dir;
			$this->data_type = DOWNLOAD_FILE;
		}
		else 
		{
			$this->content_null = true;
		}
	}

	//设置下载字符串
	function setByData($data)
	{
		if ($data == '')
		{
			$this->content_null = true;
		}
		else
		{
			$this->data = $data;
			$this->data_type = DOWNLOAD_STR;
		}
	}
	
	//设置跳转
	function setByUrl($data)
	{
		$this->data = $data;
		$this->data_len = 0;
		$this->data_type = DOWNLOAD_REDIRECT;
	}
	
	//设置模式为断点续传
	function setSeekRange($range)
	{
		$this->seek_range = $range;
	}
	
	function setDateMod($date)
	{
		$this->data_mod = $date;
	}

	function setLastmodTime($time)
	{
		$time = intval($time);
		if ($time <= 0) 
		{
			$time = time();
		}
		
		$this->data_mod = $time;
	}
	
	function setUserAuth($useauth)
	{
		$this->use_auth = $useauth;
	}

	function setMimeType($mime)
	{
		$this->mime = $mime;
	}
}
?>