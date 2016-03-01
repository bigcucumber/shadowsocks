# shadowsocks
shadowsocks climb wall to visit google for windows7. (with automate update server address, password, port script)

## Install Guide
* First start shadowsocks.exe pragram and configure server、password、port manually. You can get server、password、port from [iShadowsocks Official Site.](http://www.ishadowsocks.com/). This operation will `generate pac.txt and gui-config.json` files

* Open `ServerUpdate.py` python script. Locate to line 11 and configure your shadowsocks path  

    ISHADOWSCOKS_PROCESS_PATH = "D:/Services/shadowsocks/Shadowsocks.exe"
* You also can change proxy server. default `C` Server. Options `"A" => USA , "B" => HongKong, "C" => Japan`

    SERVER_TYPE = "C"

* Add this script to window crontab task list.
