# ORACLE 11G R2静默安装，配置

## 目录
    
* [准备工作](#-2)
* [开始安装](#-3)
* [安装后工作](#-4)
    *   [建库](#-5)
    *   [配置监听](#-6)
* [测试](#-7)

## 准备工作

1.依赖性准备
        
    #yum install make
    #yum install gcc
    #yum install binutils
    #yum install gcc-c++
    #yum install compat-libstdc++
    #yum install elfutils-libelf-devel
    #yum install elfutils-libelf-devel-static
    #yum install ksh
    #yum install libaio
    #yum install libaio-devel
    #yum install numactl-devel
    #yum install sysstat
    #yum install unixODBC
    #yum install unixODBC-devel
    #yum install pcre-devel

2.用户和组准备
    
    #groupadd oinstall
    #groupadd dba
    #useradd -g oinstall -G dba -d /home/oracle oracle
    #passwd oracle          //设置oracle密码

3.内核参数调整
    
    #vim /etc/sysctl.conf 在文件最后增加
    fs.aio-max-nr = 1048576
    fs.file-max = 6553600
    kernel.shmall = 2097152
    kernel.shmmax = 2147483648
    kernel.shmmni = 4096
    kernel.sem = 250 32000 100 128
    net.ipv4.ip_local_port_range = 1024 65000
    net.core.rmem_default = 262144
    net.core.rmem_max = 4194304
    net.core.wmem_default = 262144
    net.core.wmem_max = 1048586
    保存文件。
    #/sbin/sysctl -p          //让参数生效
 
4.用户的限制文件修改
    
    #vim /etc/security/limits.conf 在文件后增加
    oracle           soft    nproc           2047
    oracle           hard    nproc           16384
    oracle           soft    nofile          1024
    oracle           hard    nofile          65536
    oracle           soft    stack           10240
    保存文件。
    
    修改/etc/pam.d/login文件，增加如下：
    session  required   /lib64/security/pam_limits.so  //64为系统，千万别写成/lib/security/pam_limits.so，否则导致无法登录
    session     required      pam_limits.so
    修改/etc/profile,增加：
    if [ $USER = "oracle" ]; then 
     if [ $SHELL = "/bin/ksh" ]; then 
      ulimit -p 16384 
      ulimit -n 65536 
     else 
      ulimit -u 16384 -n 65536 
     fi 
    fi

5.目录准备及权限调整

    #mkdir -p /export/servers/oracle/11.2.0  //数据库系统安装目录
    #mkdir /export/data/oradata    //数据库数据安装目录
    #mkdir /export/data/oradata_back  //数据备份目录
    #mkdir /home/oracle/inventory //清单目录
    #chown -R oracle:oinstall /export/servers/oracle
    #chown -R oracle:oinstall /home/oracle/inventory
    #chown -R oracle:oinstall /export/data
    #chomod -R 775 /export/servers/oracle
    #chomod -R 775 /export/data

6.oracle安装包准备
    
下载并解压安装包到 /home/oracle/database

## 开始安装

1.打开另外一个终端，用oracle用户登录
    
2.复制并修改应答文件
    
复制应答文件

    $cp -R /home/oracle/database/response/db_install.rsp  /home/oracle/database/response/my_db_install.rsp
    #复制一份模板文件，以便改错后回滚

修改应答文件

    $vim /home/oracle/database/response/my_db_install.rsp,按实际情况修改以下项
        oracle.install.option=INSTALL_DB_SWONLY
        ORACLE_HOSTNAME=oracle11g.jd.com
        UNIX_GROUP_NAME=oinstall
        INVENTORY_LOCATION=/home/oracle/inventory/
        ORACLE_HOME=/export/servers/oracle/11.2.0
        ORACLE_BASE=/export/servers/oracle
        oracle.install.db.InstallEdition=EE
        oracle.install.db.isCustomInstall=false
        oracle.install.db.DBA_GROUP=dba
        oracle.install.db.OPER_GROUP=dba
        DECLINE_SECURITY_UPDATES=true

3.根据应答文件，开始安装

     $cd /home/oracle/database/
     $./runInstaller -silent -responseFile /home/oracle/database/response/my_db_install.rsp
4.按提示切换到root用户的终端，依次执行脚本
    
    #/home/oracle/inventory/orainstRoot.sh
    #/opt/oracle/11.2.0/root.sh
    脚本位置会提示

5.切换到oracle用户的终端，敲”回车“，完成安装

6.修改环境变量

    $vim ~/.bash_profile
    添加以下内容
    export ORACLE_BASE=/export/servers/oracle
    export ORACLE_HOME=$ORACLE_BASE/11.2.0
    export PATH=$PATH:$ORACLE_HOME/bin
    export LD_LIBRARY_PATH=$ORACLE_HOME/lib:/lib:/usr/lib
    CLASSPATH=$ORACLE_HOME/JRE:$ORACLE_HOME/jlib:$ORACLE_HOME/rdbms/jlib
    export CLASSPATH
    PATH=$PATH:/usr/bin:/etc:/usr/sbin:/usr/ucb:$HOME/bin:/usr/bin/X11:$ORACLE_HOME/bin:/oracle/OPatch:/sbin:/usr/vac/bin:.
    export PATH
7.执行`$source ~/.bash_profile` 刷新环境变量

## 安装后工作

#### 建库
1.复制并修改建库应答文件

复制应答文件

    $cp -R /home/oracle/database/response/dbca.rsp  /home/oracle/database/response/my_dbca.rsp    

修改应答文件

    $vim /home/oracle/database/response/my_dbca.rsp
    修改以下项
    OPERATION_TYPE = "createDatabase"
    GDBNAME = "db1"
    SID = "orcl11g"
    SYSPASSWORD = "123456"
    SYSTEMPASSWORD = "123456"
    DATAFILEDESTINATION = /export/data/oradata
    RECOVERYAREADESTINATION = /export/data/oradata_back
    SYSDBAUSERNAME = "system"
    SYSDBAPASSWORD = "123456"
    #以上2项可选
    INSTANCENAME = "orcl11g"
    CHARACTERSET = "UTF-8" #按需求设置，建议使用UTF-8
    NATIONALCHARACTERSET= "UTF8" #可选 "UTF8" or "AL16UTF16" 建议UTF-8
    TOTALMEMORY = "5120" #Oracle使用的最大内存，单位M建库

2.使用dbca静默建库

    $dbca -silent -responseFile /home/oracle/database/response/my_dbca.rsp

#### 配置监听

1.使用netca静默方式创建监听
    
    $netca /silent /responsefile /home/oracle/database/response/netca.rsp
    执行完成会在 $ORACLE_HOME/network/admin目录下生成sqlnet.ora和listener.ora两个文件。

2.注册sid
    
    vim $ORACLE_HOME/network/admin/listener.ora
    在
     LISTENER =
    (DESCRIPTION_LIST =
     (DESCRIPTION =
        (ADDRESS = (PROTOCOL = IPC)(KEY = EXTPROC1521))
        (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
     )
     )
     之前添加以下内容：
    SID_LIST_LISTENER =
    (SID_LIST =
        (SID_DESC =
        (SID_NAME = orcl11g)
        (ORACLE_HOME = /export/servers/oracle/11.2.0)
        (PROGRAM = extproc)
        )
    )
3.执行`$lsnrctl reload`重启监听

4.编辑 `/etc/oratab` 把 `orcl11g:/export/servers/oracle/11.2.0:N`的‘N’，改为‘Y’，这样就可以通过`dbstart`启动此实例，也可以通过`dbshut`关闭此实例了。

## 测试

1.查看监听状态
    
    $lsnrctl status
    类似以下返回，说明监听状态正常
        Service "ORCL" has 1 instance(s).
        Instance "orcl11g", status UNKNOWN, has 1 handler(s) for this service...
        Service "db1" has 1 instance(s).
        Instance "orcl11g", status READY, has 1 handler(s) for this service...
        Service "orcl11gXDB" has 1 instance(s).
        Instance "orcl11g", status READY, has 1 handler(s) for this service... 

2.sqlplus连接测试
    
    $export $ORACLE_SID=orcl11g
    $sqlplus / as sysdba
    正常登陆说明实例正常启动。

## 文档约定

    #开头的命令  说明需要使用root用户执行
    $开头的命令  说明需要用oracle用户执行












