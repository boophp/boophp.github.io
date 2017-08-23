#### 本地缓存与分布式缓存

PHP效率和速度的优化有三个问题和三个解决方案
- 动态语言------>转成静态
- 解释性语言---->
- 引擎效率低---->

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

opcode角色？
opcode 带来的好处
不用读取本地文件，模板只解析一次，极大减少IO
VLD扩展



