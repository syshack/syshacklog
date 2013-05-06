# mysql调优（innodb引擎）


## 对性能影响最大的2个参数：

---

### innodb_buffer_pool_size


InnoDB最重要的设置，这个参数确定了要预留多少内存来缓存表数据和索引,对InnoDB性能有决定性的影响，在内存允许的情况下设置比InnoDB tablespaces大10%的内存大小。

### innodb_flush_logs_at_trx_commit


可选项为0，1，2，安全级别为 0 < 2 < 1 


默认为1，每进行一次事务，就刷新一次日志。

为2时，在每个事务提交时，日志缓冲被写到文件，但不对日志文件做到磁盘操作的刷新。

为0时，日志缓冲每秒一次地被写到日志文件，并且对日志文件做到磁盘操作的刷新。任何mysqld进程的崩溃会删除崩溃前最后一秒的事务。

## IO相关参数

### innodb_flush_method

设置InnoDB同步IO的方式：
1.Default – 使用fsync（）

2.O_SYNC 以sync模式打开文件，通常比较慢。

3.O_DIRECT，在Linux上使用Direct IO。可以显著提高速度，特别是在RAID系统上。避免额外的数据复制和double buffering（mysql buffering 和OS buffering）。

### innodb_max_dirty_pages_pct

控制Innodb的脏页在缓冲中在那个百分比之下，值在范围1-100,默认为90.

###  innodb_file_io_threads 

指定InnoDB表可用的文件I／O线程数，开发者建议设置为4

## 日志相关参数

### innodb_log_buffer_size 

此参数确定些日志文件所用的内存大小，以M为单位。缓冲区更大能提高性能，但意外的故障将会丢失数据.开发者建议设置为1－8M之间 


### innodb_log_files_in_group 

为提高性能，MySQL可以以循环方式将日志文件写到多个文件。推荐设置为3

### innodb_log_file_size

确定数据日志文件的大小，以M为单位，更大的设置可以提高性能，但也会增加恢复故障数据库所需的时间,2G内为佳。
