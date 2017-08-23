<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'xcache.php';
$xcache_handle = CXCache::getInstance();

var_dump($xcache_handle->xset("UCAI_KEY", "CHINA"));