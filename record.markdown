# 本地缓存与分布式缓存

PHP效率和速度的优化有三个问题和三个解决方案
- 动态语言------>转成静态
- 解释性语言---->使用opcode缓存
- 引擎效率低---->做扩展或者PHP7、HHVM

本地缓存的基础-opcode优化
本地缓存的介绍和应用
多服务器分布式缓存
缓存数据存储与一致性hash


web服务器上的本机缓存

一致性hash，为什么有用？怎么来的？
memcache是怎么样做到一致性Hash
底层算法怎么实现
总结缓存

cig model init -> tipi
fpm情况下，不需要每次都需要加载


opcode 什么时候发挥作用
- zend opcache（推荐，官方的，相对来说靠谱）
- APC
- XCache
- eAccelerator

这个缓存在我们php运行完成后、被解析后的结果会不会保存在内存中？
会保存到内存中

没台机器上安装memcache也能达到相同效果

opcode角色？
opcode 带来的好处？

不用读取本地文件，模板只解析一次，极大减少IO

# VLD扩展

php -m | grep vld

百度搜索：vld 下载 php

cd /var/software
wget url
tar -zxvf file.tar
phpize
./configure --with-php-config=/usr/bin/php-config
make && make install
history|tail
php55 -m -d "extension=vld.so"

```php
<?php

$sum=0;
for($i=0; $i<=100;$i++)
{
    $sum+=$i;
}

echo $sum."\n";
```
内部结构查看
php55 -d"extension=vld.so" -dvld.active=1 -dvld.verbosity=3 -dvld.save_dir="./" -dvld.save_paths=1 -dvld.dump_paths=1 sum.php

ab -c 20 -n 1000 http://url..

make test?

zend opcache 与 apc 不要同时使用

可以根据命令行程序和php-fpm程序 使用不同的php.ini
好处：memory_limit time_limit
命令行程序可以放大，但是对于webserver放小点儿


借助APC工具共享内存
本地缓存是基于共享内存而实现的

在扩展开发中可以调用别的扩展中的函数


一致性hash

分布式缓存，不同机器
一台机器挂掉会导致百分之六十缓存数据挂掉


不管你机器顺序如何打乱
同样的key存储在同样的服务器上

diff file.txt file2.txt | grep ">" | wc -l
cat file.txt | cut -d' ' -f1| sort | uniq -c

ls -ltr
rz -y


刘吉发票的扩展安装和了解


写篇日记
记录七夕节礼物
记录我们的约定

# 数据库分库分表





















