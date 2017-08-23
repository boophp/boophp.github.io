<?php

/**
 * 用户读取数据库配置，并转化成 yaml
 *
 * @package
 * @subpackage
 * @author wuxing
 */
class DDb_Config
{

    public static $table_splitfield_map = array(
        "user" => 'uid',
        "album" => "albumid",
        "group" => "gid",
        "goods" => "gid",
        "cart" => "cartid",
        "category" => "catid",
        "photo"=>"pid"
    );

    public static function getTableConfig($kind)
    {
        list($first, $obj) = explode("_",$kind);
        $id_field = self::$table_splitfield_map[$obj];
        return array(
            "id_field" => $id_field
        );
    }

    //// cg_server_setting	
    public function getServer($sid)
    {
        return DDb_DBConfig::getTableRow(DDb_Const::SERVER_TABLE_KIND, "sid='" . mysql_escape_string($sid) . "'");
    }

    public function getServerByHost($host, $port)
    {
        return DDb_DBConfig::getTableRow(DDb_Const::SERVER_TABLE_KIND, "host='" . mysql_escape_string($host) . "' AND port='" . mysql_escape_string($port) . "'");
    }

    public function getSlavesByMaster($master_sid)
    {
        return DDb_DBConfig::getTableRow(DDb_Const::SERVER_TABLE_KIND, "master_sid='" . mysql_escape_string($master_sid) . "'");
    }

    public function getServers()
    {
        return DDb_DBConfig::getTableRows(DDb_Const::SERVER_TABLE_KIND, "1=1");
    }

    public function getServerBySids($sids)
    {
        if (!$sids || empty($sids))
        {
            return array();
        }
        $table = DDb_Const::SERVER_TABLE_KIND;
        return DDb_DBConfig::getTableRows($table, "sid in ('" . implode("','", $sids) . "')");
    }

    /**
     *
     * @param type $host  主机 IP
     * @param type $user 用户名
     * @param type $passwd 密码
     * @param type $master_sid 主库编号，是主库则此值 为 0
     * @param type $port 端口号
     * @param type $active 是否上线
     * @param type $backup 是否是备份机
     * @param type $remark  服务器描述
     */
    public function addServer($host, $user, $passwd, $master_sid = 0, $port = 3306, $active = 1, $backup = 0, $remark = "")
    {
        $arr = array(
            "host" => $host,
            "user" => $user,
            "master_sid" => $master_sid,
            "port" => $port,
            "passwd" => $passwd,
            "active" => $active,
            "backup" => $backup,
            "remark" => $remark
        );
        return DDb_DBConfig::insert(DDb_Const::SERVER_TABLE_KIND, $arr);
    }

    public function updateActive($sid, $active = 1)
    {
        $arr = array(
            "active" => $active
        );
        return DDb_DBConfig::update(DDb_Const::SERVER_TABLE_KIND, $arr, "sid='" . mysql_escape_string($sid) . "'");
    }

    public function updateBackup($sid, $backup = 0)
    {
        $arr = array(
            "backup" => $backup
        );
        return DDb_DBConfig::update(DDb_Const::SERVER_TABLE_KIND, $arr, "sid='" . mysql_escape_string($sid) . "'");
    }

    public function deleteServer($sid)
    {
        return DDb_DBConfig::delete(DDb_Const::SERVER_TABLE_KIND, "sid='" . mysql_escape_string($sid) . "'");
    }

    //cg_kind_setting

    public function getKinds()
    {
        return DDb_DBConfig::getTableRows(DDb_Const::KIND_TABLE_KIND, "1=1");
    }

    public function addKind($kind, $splitfield, $table_num = 1, $remark = "")
    {
        $table_prefix = $kind;
        $arr = array(
            "kind" => $kind,
            "table_num" => $table_num,
            "id_field" => $splitfield,
            "remark" => $remark,
            "table_prefix" => $table_prefix
        );
        return DDb_DBConfig::insert(DDb_Const::KIND_TABLE_KIND, $arr);
    }

    public function updateKind($kind, $splitfield, $table_num = 1, $remark = "")
    {
        $arr = array(
            "id_field" => $splitfield,
            "table_num" => $table_num,
            "remark" => $remark
        );
        return DDb_DBConfig::update(DDb_Const::KIND_TABLE_KIND, $arr, "kind='" . mysql_escape_string($kind) . "'");
    }

    public function deleteKind($kind)
    {
        return DDb_DBConfig::delete(DDb_Const::KIND_TABLE_KIND, "kind='" . mysql_escape_string($kind) . "'");
    }

    // cg_table_setting
    public function getTables()
    {
        return DDb_DBConfig::getTableRows(DDb_Const::TABLE_TABLE_KIND, "1=1");
    }

    public function addTable($kind, $sid, $tblno = 0, $db_name = "app_cntv")
    {
        $arr = array(
            "kind" => $kind,
            "no" => $tblno,
            "sid" => $sid,
            "db_name" => $db_name
        );
        return DDb_DBConfig::insert(DDb_Const::TABLE_TABLE_KIND, $arr);
    }

    public function updateTable($kind, $no, $sid)
    {
        $arr = array(
            "sid" => $sid
        );
        $where = "kind='" . mysql_escape_string($kind) . "' AND no='" . mysql_escape_string($no) . "'";
        return DDb_DBConfig::update(DDb_Const::TABLE_TABLE_KIND, $arr, $where);
    }

    public function deleteTableByTable($kind, $no)
    {
        $where = "kind='" . mysql_escape_string($kind) . "' AND no='" . mysql_escape_string($no) . "'";
        return DDb_DBConfig::delete(DDb_Const::TABLE_TABLE_KIND, $where);
    }

    public function deleteTableByKind($kind)
    {
        $where = "kind='" . mysql_escape_string($kind) . "'";
        return DDb_DBConfig::delete(DDb_Const::TABLE_TABLE_KIND, $where);
    }

    public function getTableByKind($kind)
    {
        $where = "kind='" . mysql_escape_string($kind) . "'";
        return DDb_DBConfig::getTableRows(DDb_Const::TABLE_TABLE_KIND, $where);
    }

    public function addMemcache($host, $port)
    {
        $arr = array(
            "host" => $host,
            "port" => $port,
            "active" => 1
        );
        $kind = DDb_Const::MEMCACHE_TABLE_KIND;
        return DDb_DBConfig::insert($kind, $arr);
    }

    public function updateMemcache($host, $port, $active = 1)
    {
        $arr = array(
            "active" => $active
        );
        $kind = DDb_Const::MEMCACHE_TABLE_KIND;
        $where = " host='" . mysql_escape_string($host) . "' AND port='" . mysql_escape_string($port) . "'";
        return DDb_DBConfig::update($kind, $arr, $where);
    }

    public function deleteMemcache($host, $port)
    {
        $kind = DDb_Const::MEMCACHE_TABLE_KIND;

        $where = " host='" . mysql_escape_string($host) . "' AND port='" . mysql_escape_string($port) . "'";
        return DDb_DBConfig::delete($kind, $where);
    }

    public function getMemcacheServer($host, $port)
    {
        $kind = DDb_Const::MEMCACHE_TABLE_KIND;

        $where = " host='" . mysql_escape_string($host) . "' AND port='" . mysql_escape_string($port) . "'";

        return DDb_DBConfig::getTableRow($kind, $where);
    }

    public function getMemcacheServers($active = 1)
    {
        $kind = DDb_Const::MEMCACHE_TABLE_KIND;
        $where = array("active" => $active);
        return DDb_DBConfig::getTableRows($kind, $where);
    }

}

?>
