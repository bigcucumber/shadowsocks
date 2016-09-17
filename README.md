# shadowsocks

[shadowsocks](ishadowsocks.com) 是一个代理翻墙的工具. (官方网站上有免费的帐号, 就是每隔6小时更新一次), 此仓库主要实现了(php, python, go)等语言实现的自动抓去官方网站的帐号密码, 然后填充到本地的gui-config.json文件中, 免去了手动填写的步骤， 再配合计划任务(每个自定义时间按执行脚本)实现全自动翻墙。

## 要求

*  只支持windows用户
*  python脚本需要有python运行环境, php脚本需要有php运行环境, go脚本无需任何环境(推荐, 刚学go, 代码有待完善, 目前只支持自动获取日本代理)

## 安装

* 克隆仓库: `git clone https://github.com/bigcucumber/shadowsocks.git`, 到自己定义的目录, 架设为$HOME

* 配置
    * python脚本: 编辑ServerUpdate.py, 修改$HOME,是你仓库克隆目录

        ```python
            ISHADOWSCOKS_PROCESS_PATH = "$HOME/Shadowsocks.exe"  # $HOME为 shadowsocks.exe文件所在的目录
            SERVER_TYPE = "C"  # "A" => USA , "B" => HongKong, "C" => Japan  # 选择的代理服务器
        ```
    * php脚本: 编辑ServerUpdate.php, 修改$HOME, 是你仓库克隆目录

        ```php
            $typeServer = 'B'; // ['A', 'B', 'C'] 选用服务器地址 A表示美国, B表示香港, C表示日本
            $appPath = "$HOME/Shadowsocks.exe"; // 安装目录
        ```
    * go脚本: 无需配置, 目前支持获取日本的服务器(个人测试, 大陆地区最快的).

* 添加到计划任务中去, [不会?](http://jingyan.baidu.com/article/ca00d56c767cfae99febcf73.html)

    * python添加, 主要注意`操作这块`

        ![操作](http://f.hiphotos.baidu.com/exp/w=480/sign=0c0a912a5f6034a829e2b989fb1349d9/f9dcd100baa1cd11e7a46a0bba12c8fcc3ce2d8e.jpg)
    注意**起始于**要指到shadowsocks目录下 脚本为`python ServerUpdate.py`(默认python添加到环境变量中了, 如果没有, 可制定全路径)

    * php添加, 同上, 脚本为`php ServerUpdate.php` (默认python添加到环境变量中了, 如果没有, 可制定全路径)

    * go添加很简单, **起始于**也需要指定到shadowsocks.exe目录下, 脚本为`$HOME/start.bat`



* 最后go脚本只要是httpgeter.exe在工作, 他获取远程帐号密码, 自动填写到gui-config.json文件中. 源码为httpgeter.go, 刚学, 代码不完善, 后期完善. 老鸟多多指教.



