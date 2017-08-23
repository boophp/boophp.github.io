<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * 数据库简易操作类
 *
 *
 * @author wuxing <cyber4cn@gmail.com>
 * @version $Id$
 * @package db
 *
 */
class DDb_Handle
{

    /**
     * 构造函数
     *
     * @param bool $do_replication 是否支持主从分离，true:支持，false:不支持，默认为true
     * @return void
     */
    /* function __construct($host = DB_HOST, $port = DB_PORT, $user=DB_USER, $pass=DB_PASS, $dbname=DB_NAME, $charset ='UTF8')
      {
      $this->port = $port;
      $this->host = $host;
      $this->user = $user;
      $this->pass = $pass;
      $this->dbname = $dbname;
      //set default charset as utf8
      $this->charset = $charset;
      } */
    static $dbpool = array();

    function __construct($host, $port, $user, $pass, $dbname, $charset = 'utf8')
    {
        if (defined("SAE_ACCESSKEY"))
        {
            return new DDb_SaeHandle();
        }

        $this->port = $port;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        //set default charset as utf8
        $this->charset = $charset;
    }

    /**
     * 设置当前连接的字符集 , 必须在发起连接之前进行设置
     *
     * @param string $charset 字符集,如GBK,GB2312,UTF8
     * @return void
     */
    public function setCharset($charset)
    {
        return $this->set_charset($charset);
    }

    /**
     * 同setCharset,向前兼容
     *
     * @param string $charset
     * @return void
     * @ignore
     */
    public function set_charset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * 运行Sql语句,不返回结果集
     *
     * @param string $sql
     * @return mysqli_result|bool
     */
    public function runSql($sql)
    {
        global $glog;
        $glog = new DUtil_Log();
        $this->last_sql = $sql;
        $glog->addFileLog("debug", $this->last_sql);
        //  $glog->addFileLog("debug", var_export(debug_backtrace(), true));
        $dblink = $this->db_read();
        if ($dblink === false)
        {
            return false;
        }
        $ret = mysql_query($sql, $dblink);
        $this->save_error($dblink);
        return $ret;
    }

    /**
     * 运行Sql,以多维数组方式返回结果集
     *
     * @param string $sql
     * @return array 成功返回数组，失败时返回false
     */
    public function getData($sql)
    {
        $this->last_sql = $sql;
        $data = Array();
        $i = 0;
        $dblink = $this->db_read();
        if ($dblink === false)
        {
            return false;
        }
        $result = mysql_query($sql, $dblink);

        $this->save_error($dblink);

        if (is_bool($result))
        {
            return $result;
        }
        else
        {
            while ($Array = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                $data[$i++] = $Array;
            }
        }

        mysql_free_result($result);

        if (count($data) > 0)
            return $data;
        else
            return array();
    }

    public function getCount($table, $where = "")
    {
        $sql = "SELECT COUNT(*) AS count FROM " . $table;
        if ($where)
        {
            $sql .=" WHERE" . $where;
        }
        $data = $this->getLine($sql);
        //print_r($data);
        return $data['count'];
    }

    /**
     * 运行Sql,以数组方式返回结果集第一条记录
     *
     * @param string $sql
     * @return array 成功返回数组，失败时返回false
     */
    public function getLine($sql)
    {
        $data = $this->getData($sql);
        if ($data)
        {
            return @reset($data);
        }
        else
        {
            return false;
        }
    }

    /**
     * 运行Sql,返回结果集第一条记录的第一个字段值
     *
     * @param string $sql
     * @return mixxed 成功时返回一个值，失败时返回false
     */
    public function getVar($sql)
    {
        $data = $this->getLine($sql);
        if ($data)
        {
            return $data[@reset(@array_keys($data))];
        }
        else
        {
            return false;
        }
    }

    /**
     * 同mysqli_affected_rows函数
     *
     * @return int 成功返回行数,失败时返回-1
     */
    public function affectedRows()
    {
        $result = mysql_affected_rows($this->db);
        return $result;
    }

    /**
     * 同mysqli_insert_id函数
     *
     * @return int 成功返回last_id,失败时返回false
     */
    public function lastId()
    {
        $result = mysql_insert_id($this->db);
        return $result;
    }

    /**
     * 关闭数据库连接
     *
     * @return bool
     */
    public function closeDb()
    {
        if (isset($this->db))
        {
            @mysql_close($this->db);
            $this->db = null;
        }
    }

    /**
     *  同mysqli_real_escape_string
     *
     * @param string $str
     * @return string
     */
    public function escape($str)
    {
        return mysql_escape_string($str);
    }

    /**
     * 返回错误码
     *
     *
     * @return int
     */
    public function errno()
    {
        return $this->errno;
    }

    /**
     * 返回错误信息
     *
     * @return string
     */
    public function error()
    {
        if (isset($this->sql))
        {
            echo $this->sql;
        }
        return $this->error;
    }

    /**
     * 返回错误信息,error的别名
     *
     * @return string
     */
    public function errmsg()
    {
        return $this->error();
    }

    /**
     * @ignore
     */
    private function connect($is_master = true)
    {
        if ($this->port == 0)
        {
            $this->error = 13048;
            $this->errno = 'Not Initialized';
            return false;
        }

        //$db = mysqli_init();
        //mysql_options($db, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
        //echo $this->host . ":" . $this->port, $this->user, $this->pass;
        //连接复用
        $key = $this->host . ":" . $this->port . $this->user . $this->pass;
        if (isset(self::$dbpool[$key]) && mysql_ping(self::$dbpool[$key]))
        {
            $db = self::$dbpool[$key];
        }
        else
        {
            if (!$db = mysql_connect($this->host . ":" . $this->port, $this->user, $this->pass))
            {
                echo "connected failed:\n";
                return false;
            }
            self::$dbpool[$key] = $db;
        }
   //     echo "<font color=red>{$this->dbname} {$this->last_sql}</font><br />";
        if (mysql_select_db($this->dbname, $db))
        {
            $this->save_error($db);
        }
        return $db;
    }

    /**
     * @ignore
     */
    private function db_read()
    {
         /*if ($this->db)
        {
            return $this->db;
        }*/        
        $this->db = $this->connect(false);

        $sql = "SET NAMES " . $this->charset;
        mysql_query($sql, $this->db);
        return $this->db;
    }

    /**
     * @ignore
     */
    private function save_error($dblink)
    {

        $this->errno = mysql_errno($dblink);
        if ($this->errno)
        {
            $this->error = mysql_error($dblink);
            echo $this->errno . " " . $this->error . "\n<br />";
            echo $this->last_sql . "\n<br />";
            //print_r(debug_backtrace());
        }
    }

    private $error;
    private $errno;
    private $last_sql;
    private $db;

}