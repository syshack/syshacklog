# mysql调优（innodb引擎）


## 对性能影响最大的2个参数：

---

### innodb_buffer_pool_size

InnoDB最重要的设置，这个参数确定了要预留多少内存来缓存表数据和索引,对InnoDB性能有决定性的影响，在内存允许的情况下设置比InnoDB tablespaces大10%的内存大小。建议：尽可能大

### innodb_flush_logs_at_trx_commit 经常配合 sync_binlog设置

可选项为0，1，2，安全级别为 0 < 2 < 1 

默认为1，每进行一次事务，就刷新一次日志。

为2时，在每个事务提交时，日志缓冲被写到文件，但不对日志文件做到磁盘操作的刷新。

为0时，日志缓冲每秒一次地被写到日志文件，并且对日志文件做到磁盘操作的刷新。任何mysqld进程的崩溃会删除崩溃前最后一秒的事务。

sync_binlog=N

N>0  — 每向二进制日志文件写入N条SQL或N个事务后，则把二进制日志文件的数据刷新到磁盘上；

N=0  — 不主动刷新二进制日志文件的数据到磁盘上，而是由操作系统决定；

推荐配置组合：
N=1,1  安全性非常高，磁盘IO写能力足够支持业务，比如充值消费系统；

N=1,0 安全性高，磁盘IO写能力支持业务不富余，允许备库落后或无复制；

N=2,0或2 安全性一般，允许丢失一点事务日志，复制架构的延迟也能接受；

N=0,0 安全性差，磁盘IO写能力有限，无复制或允许复制延迟稍微长点能接受，例如：日志性登记业务；

## 内存相关

### innodb_additional_mem_pool_size

一般设置到16M就可以了，如果需要增加mysql错误日志会记录。

## IO/文件相关参数

### innodb_file_per_table

启用单表空间，减少共享表空间维护成本，减少空闲磁盘空间释放的压力。大数据量情况下会有性能上的提升，为此建议大家使用独立表空间代替共享表空间的方式；

### innodb_flush_method

设置InnoDB同步IO的方式：

1.Default – 使用fsync（）

2.O_SYNC 以sync模式打开文件，通常比较慢。

3.O_DIRECT，在Linux上使用Direct IO。可以显著提高速度，特别是在RAID系统上。避免额外的数据复制和double buffering（mysql buffering 和OS buffering）。

### innodb_max_dirty_pages_pct

控制Innodb的脏页在缓冲中在那个百分比之下，值在范围1-100,默认为90，90性能最好，不过重启恢复时间最长。

###  innodb_file_io_threads 

指定InnoDB表可用的文件I／O线程数，开发者建议设置为4

## 缓存相关

### binlog_cache_size

一般场景2-4M，可以通过binlog_cache_use 以及 binlog_cache_disk_use来分析设置的binlog_cache_size是否足够，是否有大量的binlog_cache由于内存大小不够而使用临时文件（binlog_cache_disk_use）来缓存了。

### bulk_insert_buffer_size: 

如果经常性的需要批量插入大量数据，可以适当调大，原则上不超过32M。

## 日志相关参数

### innodb_log_buffer_size 

此参数确定些日志文件所用的内存大小，以M为单位。缓冲区更大能提高性能，但意外的故障将会丢失数据.开发者建议设置为1－8M之间 

### innodb_log_files_in_group 

为提高性能，MySQL可以以循环方式将日志文件写到多个文件。推荐设置为3

### innodb_log_file_size

确定数据日志文件的大小，以M为单位，更大的设置可以提高性能，但也会增加恢复故障数据库所需的时间,2G内为佳。

可以使用：show status like 'Innodb_os_log_written'; select sleep(60); show status like 'Innodb_os_log_written';

查看每分钟写多大，然后确定此值的大小。

## 连接相关

### max_connect_errors

默认值为10，也即mysqld线程没重新启动过，一台物理服务器只要连接异常中断累计超过10次，就再也无法连接上mysqld服务，为此建议大家设置此值至少大于等于10W；

### wait_timeout 

与服务器端无交互状态的连接超时时间。一般建议设置为：172800（48小时），维护连接建议客户端来做，用完及时关闭连接，避免长连接。
