<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require("Block.class.php");

$rblock = new Simple\SHM\Block(1001);
echo $rblock->read();
 