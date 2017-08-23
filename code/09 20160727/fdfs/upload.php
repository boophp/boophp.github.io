<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (function_exists('fastdfs_storage_upload_by_filename'))
{
    $ret = fastdfs_storage_upload_by_filename('/var/data/ppt/classes/zhaopian/zhaopian1_01.jpg');
    var_dump($ret);
}