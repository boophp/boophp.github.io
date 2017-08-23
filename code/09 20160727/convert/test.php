<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$method_no_para_pattern = "#\-\s*\((\w+)\)\s*(\w+)\s*\{#mis";
$content = '- (void) viewDidLoad
{
    [super viewDidLoad];
    // 创建CLLocationManager对象
    self.locationManager = [[CLLocationManager alloc] init];
}';

if (preg_match_all($method_no_para_pattern, $content, $result))
{
    print_r($result);
}