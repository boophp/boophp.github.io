<?php

/**
 *
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class stat extends DCore_BackApp
{

    public function main()
    {
        $startdate = "20120501";
        $enddate = "20120821";
        $starttime = strtotime($startdate);
        $endtime = strtotime($enddate);
        for ($i = $starttime; $i < $endtime; $i+=86400)
        {
            $day = date("Ymd", $i);
            $cmd = "php stat.php date=$day ptype=0";
            echo $cmd . "\n";
            $cmd = "php stat.php date=$day ptype=1";
            echo $cmd . "\n";
        }
    }

}

$stat = new stat();
$stat->run();
?>
