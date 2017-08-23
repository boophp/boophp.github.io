<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'apc.php';
$handle = APC::getInstance();

echo '<pr>';
var_dump($handle->aget("UCAI_KEY"));
echo '</pre>';
echo '<pre>';
var_dump($handle->agetObj("UCAI_OBJ_KEY"));
echo '</pre>';
echo '<pre>';
var_dump($handle->amgetObj(array("UCAI_KEY", "UCAI_OBJ_KEY")));
echo '</pre>';