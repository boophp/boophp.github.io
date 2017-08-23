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
    $mysqli = new mysqli("101.251.196.91", "quanzhan", "5jsx2qs", "quanzhan",3308);

    /* check connection */
    if (mysqli_connect_errno())
    {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
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
