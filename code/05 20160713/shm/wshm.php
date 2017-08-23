<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require("Block.class.php");

$block = new Simple\SHM\Block(1001);
$block->write($argv[1]);

