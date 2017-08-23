<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $mysqli;

function connect_db()
{
    global $mysqli;
    $mysqli = new mysqli("127.0.0.1:3301", "quanzhan", "5jsx2qs", "quanzhan");

    /* check connection */
    checkError("connect");
}

function execute($sql)
{
    global $mysqli;
    $ret = $mysqli->query($sql);
    if (!$ret)
    {
        echo $mysqli->error;
    }
    // echo $ret;
    return $ret;
}

function query($sql)
{
    $ret = execute($sql);
    $data = array();
    while ($row = mysqli_fetch_array($ret, MYSQLI_ASSOC))
    {
        $data[] = $row;
    }
    $ret->close();
  //  $ret->free();
    return $data;
}

function ucai_real_escape_string($string)
{
    global $mysqli;
    return $mysqli->real_escape_string($string);
}

function close_db()
{
    global $mysqli;
    $mysqli->close();
}
 

function checkError($tag="")
{
    global $mysqli;
    if (mysqli_connect_errno())
    {
        $str = mysqli_connect_error();
        $fd = fopen("tmp.log", "a+");
        fwrite($fd, $tag.":".$str . "\n");
        fclose($fd);
        $mysqli->close();
        exit;
    }
    if (mysqli_errno($mysqli))
    {
        $str = mysqli_error($mysqli);
        $fd = fopen("tmp.log", "a+");
        fwrite($fd, $tag.":".$str . "\n");
        fclose($fd);
    }
   
}
