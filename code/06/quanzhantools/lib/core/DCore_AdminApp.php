<?php

/**
 *
 *
 * @package
 * @subpackage
 */
class DCore_AdminApp extends DCore_WebApp
{

    protected $ptype;
    protected $pname;
    static $tmpl_maps = array(
        DGroups_Const::GROUP_PRODUCT_COMMON => ADMIN_TEMPLATE_DIR,
        DGroups_Const::GROUP_PRODUCT_CLUB => CLUB_ADMIN_TEMPLATE_DIR,
        DGroups_Const::GROUP_PRODUCT_WEIBO => WEIQUN_ADMIN_TEMPLATE_DIR,
    );

    public function __construct($ptype = DGroups_Const::GROUP_PRODUCT_COMMON)
    {
        $this->ptype = $ptype;     
        $this->pname = DGroups_Const::$product_names[$this->ptype];
        parent::__construct();
        DCore_Template::$rootdir = self::$tmpl_maps[$ptype];
        DCore_Template::$compiledir =  ROOT_DIR . DS . "ctemplate_admin".DGroups_Const::$product_names[$ptype].DS;
        
    }
 
    protected function getParam($pName, $pDefault="", $pReqType="r", $pDataType = DCore_Input::TYPE_STR)
    {
        global $argv;
        $ret = DCore_Input::clean($pReqType, $pName, $pDataType);

        if ($ret == "" && !key_exists($pName, $_REQUEST))
        {
            if (!empty($argv))
            {
                $data = $_SERVER['argv'];
                $param = array();
                foreach ($data as $v)
                {
                    $t = explode('=', $v, 2);
                    if (count($t) == 2)
                    {
                        $param[$t[0]] = $t[1];
                    }
                }
                if (isset($param[$pName]))
                {
                    $ret = $param[$pName];
                }
            }
        }
        if (!$ret && $pDefault !== "")
        {
            $ret = $pDefault;
        }
        return $ret;
    }

    public function showError($msg, $code)
    {
        DCore_ProCache::set('notice', "[$code]:" . $msg);
        $this->outputPage();
    }

}

?>
