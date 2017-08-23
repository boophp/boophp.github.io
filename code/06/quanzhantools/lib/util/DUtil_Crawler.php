<?php

class DUtil_Crawler
{

    static function getHostIndex($hostparts)
    {
        $domain = "";
        $a_domainsuffix = array("edu", "com", "net", "org", "gov", "ac");
        $hcount = count($hostparts);
        for ($i = $hcount - 2; $i >= 0; $i--)
        {
            if (!in_array($hostparts[$i], $a_domainsuffix))
            {
                return $i;
            }
        }
        return -1;
    }

    static function getHost($url)
    {
        $path = parse_url($url);
        if (!isset($path['host']))
        {
            return "";
        }
        $host = explode(".", $path['host']);
        $hostindex = self::getHostIndex($host);
        if ($hostindex == -1)
        {
            return $path['host'];
        }
        else
        {
            return $host[$hostindex];
        }
    }

    static function getMainHost($url)
    {

        $path = parse_url($url);
        if (!isset($path['host']))
        {
            return "";
        }
        $host = explode(".", $path['host']);
        $hostindex = self::getHostIndex($host);

        if ($hostindex == -1)
        {
            return $path['host'];
        }
        else
        {
            return implode(".", array_slice($host, $hostindex));
        }
    }

    static function getCookies($header)
    {
        $cookies = array();
        $cookie = "";
        $returnar = explode("\r\n", $header);
        for ($ind = 0; $ind < count($returnar); $ind++)
        {
            if (preg_match("#Set-Cookie:#", $returnar[$ind]) || preg_match("#Cookies #", $returnar[$ind]))
            {
                $cookie = trim(str_replace("Set-Cookie:", "", $returnar[$ind]));
                $cookie = explode(";", $cookie);
                $cookvalues = explode("=", $cookie[0]);
                if ($cookvalues[1] == "")
                {
                    continue;
                }
                $cookies[trim($cookie[0])] = trim($cookie[0]);
            }
        }

        $cookie = array();
        foreach ($cookies as $key => $value)
        {
            array_push($cookie, "$value");
        }
        $cookie = implode(";", $cookie);

        return $cookie;
    }

    static function getMiddlePart($subject, $startflag, $endflag)
    {
        $partstart = strpos($subject, $startflag) + strlen($startflag);
        $subject = substr($subject, $partstart);
        $partend = strpos($subject, $endflag);
        return trim(substr($subject, 0, $partend));
    }

    static function curl_crawl_page($url, $cookies = "", $post = false, $postvals = array(), $withheader = false, $proxy="", $referer="")
    {
        global $curl;
        $curl = curl_init();
        

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        if ($proxy)
        {
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, $withheader);
        curl_setopt($curl, CURLOPT_HEADER, $withheader);
        curl_setopt($curl, CURLOPT_ENCODING, "UTF-8");
        if (!empty($postvals))
        {
            curl_setopt($curl, CURLOPT_POST, $post);
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postvals));
        }
        if (is_array($cookies))
        {
            $tmp = array();
            foreach ($cookies as $key => $val)
            {
                $tmp[] = $key . "=" . $val;
            }
            $cookies = implode(";", $tmp);
        }

        
        curl_setopt($curl, CURLOPT_COOKIE, $cookies);
        curl_setopt($curl, CURLOPT_USERAGENT, '	Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0');
        if(!$referer)
        {
            $pathinfo = parse_url($url);
            $referer = $pathinfo['scheme'] . "://" . $pathinfo['host'];
        }
        curl_setopt($curl, CURLOPT_REFERER,$referer);
        curl_setopt($curl, CURLOPT_URL, $url);

        $page = curl_exec($curl);
      
        curl_close($curl);
        return $page;
    }

    static function isOverseas($url)
    {
        return strpos($url, "amazon.com/") !== false;
    }

}

?>
