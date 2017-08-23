<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'apc.php';
$handle = APC::getInstance();
var_dump($handle->aset("UCAI_KEY", "China"));

var_dump($handle->asetObj("UCAI_OBJ_KEY",
    array("Jianghua", "Xiaowen")));
