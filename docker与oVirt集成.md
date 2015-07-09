## docker与oVirt集成

### vm上运行docker
**准备工作**
从官方的glance服务器导入虚拟机模板（CentOS 6.5 64-Bit Docker）
**插件安装及配置**
1. 上传插件到:/usr/share/ovirt-engine/ui-plugins/
2. 修改docker.json 红色框内修改为ovirt-engine api的信息
![Alt text](./1420426837394.png)
3. 重启ovirt-engine，在虚拟机界面就会看到"Create Docker VM"的按钮。

**使用**
1. 点击"Create Docker VM"，弹出如下图界面：
![Alt text](./1420427412326.png)

2. 按提示输入信息，确认就会创建虚拟机
3. 启动虚拟机，就会同时启动docker
![Alt text](./1420427445437.png)
4. 访问192.168.21.159，会出现nginx的欢迎界面
![Alt text](./1420427560632.png)


需要注意的问题:
1. 插件依赖jquery，如果无法访问外网的话，需要把jquery下载到本地，并修改html里的引用。
2. 官方镜像的docker版本过低（0.9），无法使用私有库有问题，需要升级docker版本（1.3.2）

问题：

1. 官网的registry有跨域问题，需要本地建立个registry。


### oVirt node直接运行docker容器（草案）
目前还是草案，资料地址：
[Docker Integration](http://www.ovirt.org/Features/Docker_Integration)

1. 实现oVirt与docker镜像服务器的通讯（类似于oVirt的模板管理），存储管理界面列出增加的docker镜像源
2. 为支持docker的主机增加额外的标记，支持docker主机的部署（添加主机时选择"支持docker"，自动部署docker程序）。
3. 增强engine，支持docker容器的操作。
4. 增强vdsm，增加docker支持（操作容器的api和CLI，类似于操作vm）
5. 增加docker管理界面，支持容器的创建，编辑，运行，停止

**工作流：**
创建：添加支持docker的主机到集群并添加标记信息，容器管理界面选择docker镜像，填写port映射，文件系统映射等信息传给engine，engine选择支持docker的主机（通过标记信息确定）创建容器。
编辑/运行/停止：和vm一致，web调用engine api，engine调用vdsm的api操作docker容器。
