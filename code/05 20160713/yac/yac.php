<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$yac = new Yac();
$yac->set("foo", "bar");
$yac->set(
    array(
        "dummy" => "foo",
        "dummy2" => "foo",
        )
    );
$yac->get("dummy");
var_dump($yac->get(array("dummy", "dummy2")));
