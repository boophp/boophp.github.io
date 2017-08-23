<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//
//
//[0] => uid
//            [1] => realname
//            [2] => username
//            [3] => email
//            [4] => password
//            [5] => salt
function addUserSql($id, $username, $email, $password)
{
    $salt = rand(100000, 999999);
    $password = md5($password."#".$salt);
    $sql = "INSERT INTO ly_user(uid, username, email,password, salt) VALUES($id, '".$username."', '".$email."', '".$password."', '".$salt."')";
    return $sql;
}