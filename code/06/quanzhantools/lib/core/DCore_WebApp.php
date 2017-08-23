<?php

/* * *************************************************************************
 *
 * Copyright (c) 2011 HighPer.cn, All Rights Reserved
 *
 *
 * ************************************************************************ */

/**
 * 页面应用程序基类
 *
 * @package
 * @subpackage
 */
class DCore_WebApp extends DCore_BaseApp
{

    protected $userinfo;
    protected $uid;
    protected $pagetitle = "";
    protected $_lgmode = "";  //页面逻辑模式
//公开页面 --
//私有页面 -- pri
//宿主页面 -- host
    protected $_acmode = "";  //页面操作模式
//用户模式 --
//系统模式 -- "sys"
    protected $_needpost = false; //是否需要使用POST提交，不需要填写false，需要填写跳转地址
    protected $jscssconfigtype = "commen"; //js和css配置的编号
    protected $jscssstr = ""; //最终生成的cssjs文本块

    function __construct($pagetitle = "", $lgmode = "", $acmode = "", $needpost = false)
    {
        $this->pagetitle = $pagetitle;
        $this->_lgmode = $lgmode;
        $this->_acmode = $acmode;
        $this->_needpost = $needpost;
    }

//得加入清理参数的功能
    protected function getParam($para, $default="", $type = DCore_Input::TYPE_STR)
    {
        if (isset($_REQUEST[$para]))
        {
            return DCore_Input::clean("r", $para, $type);
        }
        return $default;
    }

    protected function checkAuth()
    {
        //放到客户端去检测
        $this->userinfo = DCntv_User::getUserInfo();
       
        $this->uid = DCntv_User::getUid();
        
        //if ($this->_acmode == 'host' && !$this->uid)
        //{
           // header('Location: /index.php#tiplogin');
            //exit;
        //}
        
        if($this->_acmode=="host")
        {
            $this->needlogin = "true";
        }
    }

    protected function outputPage()
    {
        
    }

    //集中输出js和css文件引用
    protected function outputJsCss()
    {
        $jscssconfig = DCore_Config::$jscssconfig[$this->jscssconfigtype];
        $this->jscssstr = "";
        foreach ($jscssconfig as $onejscss => $version)
        {
            if (strpos($onejscss, '.js') !== false)
            {
                $this->jscssstr .= '<script type="text/javascript" src="' . $onejscss . '?v=' . $version . '"></script>';
            }
            else if (strpos($onejscss, '.css') !== false)
            {
                $this->jscssstr .= '<link href="' . $onejscss . '?v=' . $version . '" rel="stylesheet" type="text/css">';
            }
        }
    }

    public function run()
    {
        try
        {
            
            $this->getPara();

            $this->checkPara();

            $this->checkAuth();

            $this->main();

            $this->outputJsCss();

            $this->outputPage();

            $this->exitApp();
        }
        catch (Exception $ex)
        {
            $this->showError($ex->getMessage(), $ex->getCode());
        }
    }

    protected function showError($msg, $code)
    {
        $this->outputJson($code, $msg, array());
    }

    protected function outputJson($code, $msg, $arrResult)
    {
        $arrJson = array("code" => $code,
            "msg" => $msg,
            "info" => $arrResult
        );
        // DUtil_Debug::pr($arrJson);
        echo json_encode($arrJson);
        exit;
    }

}

?>
