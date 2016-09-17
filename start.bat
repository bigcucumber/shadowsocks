@echo off

taskkill /F /IM shadowsocks.exe
taskkill /F /IM ss_privoxy.exe
echo "shadowsocks.exe killed!"

RunHiddenConsole.exe httpgeter.exe

echo "httpgeter.exe started background!"
