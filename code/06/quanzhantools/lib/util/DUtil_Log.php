<?php
/*
*
* 文件名称：DUtil_Log.php
* 摘    要：记录日志
*/

define("MULTILOG_TIMESLOT", "0.005");
define("MULTILOG_TIMESWITCH","0");
define("MULTILOG_EVENTSWITCH","1");
define("MULTILOG_LOGSWITCH","1");
define("MULTILOG_FILESWITCH","1");

class DUtil_Log
{
    var $pid;
    var $ip;
    var $event_fp;
    var $time_fp;
    var $log_fp;
    var $start_micro;
    var $pre_micro;

    function DUtil_Log()
    {
        $this->pid = getmypid().".".rand(0, 100000);

        $this->ip = DUtil_Ip::getIP();

        if(MULTILOG_TIMESWITCH)
        {
            $now = $this->getMicro();
            $this->setStartMicro($now);
            $this->setPreMicro($now);

            $time_file = LOG_DIR."/"
                    .basename(str_replace("/", "_", $_SERVER['PHP_SELF']), ".php")
                    .".time.log";
            $this->time_fp = @fopen($time_file,"a");
        }
    }

    function _DUtil_Log(&$glog)
    {
        $status = $glog->getExitStatus();

        if ($glog->time_fp)
        {
            $glog->addTimeLog($status);
            fclose($glog->time_fp);
        }

        if ($glog->event_fp)
        {
            $glog->addEventLog($status);
            fclose($glog->event_fp);
        }

        if ($glog->log_fp)
        {
            fclose($glog->log_fp);
        }
    }

    function getExitStatus()
    {
        $status = connection_status();//0,1,2,3;
        switch($status)
        {
            case 1:
                return "User Aborted";
            case 2:
                return "Program Timeout";
            case 3:
                return "User Aborted and Program Timeout";
            default:
                return "Normal";
        }
    }

    function getMicro()
    {
        list($msec, $sec) = explode(" ", microtime());
        return ((float)$msec + (float)$sec);
    }

    function setPreMicro($now)
    {
        $this->pre_micro = $now;
    }

    function setStartMicro($now)
    {
        $this->start_micro = $now;
    }

    function getPre2Now($now)
    {
        return $now - $this->pre_micro;
    }

    function getStart2Now($now)
    {
        return $now - $this->start_micro;
    }

    function addLog($desc)
    {
        if (MULTILOG_LOGSWITCH)
        {
            if (!$this->log_fp)
            {
                $log_file = LOG_DIR."/_log.log";
                $this->log_fp = @fopen($log_file,"a");
            }
            if ($this->log_fp)
            {
                $time = date("Y-m-d H:i:s");
                $flog = sprintf("%s\t%s\t%s\t%s\n",
                        $time, $this->ip, $this->pid, $desc);
                fwrite($this->log_fp, $flog);
            }
        }
    }

    function addEventLog($desc)
    {
        if (MULTILOG_EVENTSWITCH)
        {
            if (!$this->event_fp)
            {
                $event_file = LOG_DIR."/"
                        .basename(str_replace("/", "_", $_SERVER['PHP_SELF']), ".php")
                        .".event.log";
                $this->event_fp = @fopen($event_file,"a");
            }

            if ($this->event_fp)
            {
                $time = date("Y-m-d H:i:s");
                $flog = sprintf("%s\t%s\t%s\t%s\n",
                        $time, $this->ip, $this->pid, $desc);
                fwrite($this->event_fp, $flog);
            }
        }
    }

    function addTimeLog($desc)
    {
        if ($this->time_fp)
        {
            $now = $this->getMicro();
            $pre2now = $this->getPre2Now($now);
            $this->setPreMicro($now);
            if($pre2now <= MULTILOG_TIMESLOT)
            {
                return;
            }
            $start2now = $this->getStart2Now($now);

            $time = date("Y-m-d H:i:s");
            $flog = sprintf("%s\t%s\t%s\t%s\t%s\t%s\n",
                    $time, $this->ip, $this->pid, round($pre2now, 5), round($start2now, 5), $desc);
            fwrite($this->time_fp, $flog);
        }
    }

    function addFileLog($file, $desc)
    {
        $filename = LOG_DIR."/".$file.".filelog";
        $fp = fopen($filename, "a");
        if ($fp && MULTILOG_FILESWITCH)
        {
            $time = date("Y-m-d H:i:s");
            $flog = sprintf("%s\t%s\t%s\t%s\n",
                    $time, $this->ip, $this->pid, $desc);
            fwrite($fp, $flog);
            fclose($fp);
        }
    }

    function addDebugLog($desc = "")
    {
        $dbt = debug_backtrace();
        $strs = array($desc);
        foreach ($dbt as $k => $item)
        {
            $strs[] = "#".$k."  ".$item["file"].":".$item["line"]." ".$item["function"]."()";
        }
        $this->addFileLog("debug", implode("\t", $strs));
    } 
}

$glog = new DUtil_Log();
register_shutdown_function(array("DUtil_Log", "_DUtil_Log"), $glog);
 

?>