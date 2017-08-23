<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//图片检测
global $config;
$config = include_once("config.php");


class UploadChecker
{

    function main()
    {
        global $config;
        header("Content-Type:text/html;charset=utf-8");
        $fileinfo = $_FILES['filename'];
        $tmpfile = $fileinfo['tmp_name'];
        $ftype = $_REQUEST['ftype'];
        //exif
        //图片操作的库Imagick
        //文件本身就是木马（可以通获文件信息，图片，有高宽），如果高宽，不是图片
        //文件本身就是图片，但含有木马信息
        //Image width, height(病毒）
        //Imagick(获得）实例化一对象，再后再写出成文件（干净）消毒。
        //Linux下，命令行方式的反病毒软件

       // $ftype="../../";
        //枚举的这些变量，最好做遍历检查
        if(!isset($config[$ftype]))
        {
            $ftype = "photo";
        }
        $id = 0;
        switch($ftype)
        {
            case "photo":
                $id = rand(1,20000);
                break;
            case "logo":
                $id = rand(1, 200000);
                break;
            case "appphoto":
                $id = rand(100000,200000);
                break;
        }
        $typedir = "../uploads/$ftype";
        $cmd = "mkdir -p $typedir";
        //echo $cmd;
        system($cmd);
        $ret = pathinfo($fileinfo['name'], PATHINFO_EXTENSION);
        $localfile = $typedir."/$id.$ret";
            
        move_uploaded_file($tmpfile, $localfile);
        $lfilename = dirname(dirname(__FILE__))."/uploads/$ftype/".basename($localfile);
        //
        
        echo "本地路径:".$lfilename."<br />";
      $rfilename = $this->upload_fdfs($lfilename);
        echo "FDFS路径:".$rfilename."<br />";
        $rfilename = $this->upload_php_dfs($ftype, $id, $lfilename);
            
    }

    function upload_image()
    {
        
    }

    function upload_file()
    {
        
    }
    
    function upload_php_dfs($type, $id, $filename)
    {
        global $config;
        $tconfig = $config[$type];
        
        foreach($tconfig as $key=>$modules)
        {
            //1-1000 => array(xxx,xxx)
            list($start, $end) = explode("-", $key);
            if($start<=$id && $id<$end)
            {
                $first = "";
                foreach($modules as $module)
                {
                    $cmd = "rsync $filename $module/$type/";
                    echo $cmd."<br />";
                    system($cmd);
                    if(!$first) $first =  "$module/$type/".basename($filename);

                }
                if($first)
                {
                    return $first;
                }
            }
        }
        
    }

    function upload_fdfs($filename)
    {
        if (function_exists('fastdfs_storage_upload_by_filename'))
        {
            $ret = fastdfs_storage_upload_by_filename($filename);
            return  $ret['filename'];
        }
    }

}

$checker = new UploadChecker();
$checker->main();
