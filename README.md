# shadowsocks
shadowsocks climb wall to visit google for windows7. (with automate update server address, password, port script)

## Requirement
* python runtime environment or php runtime environment.

## Install Guide
* first chone repository `git clone https://github.com/bigcucumber/shadowsocks.git`  
* config
    * python: edit ServerUpdate.py

        ISHADOWSCOKS_PROCESS_PATH = "D:/Services/shadowsocks/Shadowsocks.exe"  # D:\Services\shadowsocks/ shadowsocks.exe文件所在的目录
        SERVER_TYPE = "C"  # "A" => USA , "B" => HongKong, "C" => Japan  # 选择的代理服务器
    * php: edit ServerUpdate.py

        $typeServer = 'B'; // ['A', 'B', 'C'] 选用服务器地址 A表示美国, B表示香港, C表示日本
        $appPath = "D:/Services/shadowsocks/Shadowsocks.exe"; // 安装目录
* add to task scheduler program.


