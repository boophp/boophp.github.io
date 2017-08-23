<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DSeo_Site {

    static function sitelog($site, $uid=0, $ext='') {
        $arr['site'] = $site;
        $arr['uid'] = $uid;
        $arr['ip'] = $_SERVER['REMOTE_ADDR'];
        $arr['refer'] = $_SERVER['HTTP_REFERER'];
        $arr['ltime'] = date("Y-m-d H:i:s");
        $arr['ext'] = $ext;
        $ret = DDb_DB::insert('m_site_from', $arr);
    }

}

?>