<?php

/**
 * 将生成的 db.sql 进行切割，以便执行 newtable.php
 *
 * @package
 * @subpackage
 */
include_once("../../conf/global.php");

class db extends DCore_BackApp
{

    protected $dbfile;
    protected $dbname;

    protected function getParameter()
    {
        $this->dbfile = $this->getParam("dbfile");
         $this->dbname = $this->getParam("dbname");
    }

    protected function printUsage()
    {
        global $argc, $argv;
        echo "Usage: php " . $argv[0] . " dbfile=<dbfile> dbname=<dbname>\n";
    }

    protected function checkParameter()
    {
        if (!$this->dbfile || !is_file($this->dbfile) || !$this->dbname)
        {
            $this->printUsage();
            exit;
        }
    }

    protected function main()
    {
        $content = file_get_contents($this->dbfile);
        $newscontent = str_replace("\n", "", $content);
        $pattern = "/create\s+table(.*?)\(/i";
        if (preg_match_all($pattern, $newscontent, $result))
        {
            foreach ($result[0] as $key => $item)
            {
                $table_name = trim(str_replace(array("`"), array(""), $result[1][$key]));

                $item = str_replace("(", "", $item);
                $pos = strpos($content, $item);
                if ($pos === false)
                {
                    echo $content . "\n";
                    exit;
                }
                $content = substr($content, $pos);
                $endpos = strpos($content, "UTF8");

                $filecontent = substr($content, 0, $endpos + 7);
                $leftcontent = substr($content, $endpos + 8);
                file_put_contents($table_name . ".sql", $filecontent);
                $kind = $table_name;
                $count = 1;
                $fstart = strpos($filecontent, "(");
                $fend = strpos($filecontent, ";");
                $fcontent = substr($filecontent, $fstart + 1, $fend - $fstart);
                $fcontent = explode(" ", trim($fcontent));
                $fcontent = $fcontent[0];
                $sid = 1;
                if (substr($table_name, -1) == '0')
                {
                    $kind = substr($table_name, 0, strlen($table_name) - 2);
                    $sid = "1-2";
                    $num = 4;
                }
                else
                {
                    $num = 1;
                }
                echo "php newtable.php kind=" . $kind . "  sid=" . $sid . " db=".$this->dbname." sqlfile=$table_name.sql num=$num\n";
                //      echo $filecontent."\n";
                echo $table_name . "\n";
            }
        }
    }

}

$db = new db();
$db->run();
?>
 
