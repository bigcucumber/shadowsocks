__author__ = "luowen"
"""
Description: Automatic update ishadowsocks's server address and password.
Author: awimj <bigpao.luo@gmail.com>
Time: 2016-02-29
"""
import re, json, os, time, logging, urllib, urllib.request as request

HOSTADDR = "http://www.ishadowsocks.com/"
ISHADOWSCOKS_PROCESS_NAME = "Shadowsocks.exe"
ISHADOWSCOKS_PROCESS_PATH = "D:/Services/shadowsocks/Shadowsocks.exe"
SERVER_TYPE = "C"  # "A" => USA , "B" => HongKong, "C" => Japan


def updateServer(proxyServerType = "C"):
    """
        first: fetch server address and password from ishadowsocks website
        second: fetch success then merger ishadowsocks client configure file
    """
    serverInfo = fetchIshadowsocks(proxyServerType)
    if serverInfo and serverInfo.get("server") and serverInfo.get("password") and serverInfo.get("port"):
        # if address or password or port invalid don't merge
        if mergeLocalConfig(serverInfo):
            killProcess()
            time.sleep(2)
            startProcess()
    else:
        logMsg("'{0}' server address or password or port invalid.".format(proxyServerType))
    exit(0)

def fetchIshadowsocks(serverType):
    "fetch server address and password from ishadowsocks websiet"
    serverInfo = {}
    breakPointIndex = 0
    try:
        with request.urlopen(HOSTADDR) as urlHandle:
            for line in urlHandle:
                lineString = line.decode("utf-8");
                reObjMatchUrl = re.search("<h4>{0}服务器地址:(.*)</h4>".format(serverType), lineString)
                if reObjMatchUrl:
                    serverInfo["server"] = reObjMatchUrl.group(1)

                reObjMatchPwd = re.search("<h4>{0}密码:(.*)</h4>".format(serverType), lineString)
                if reObjMatchPwd:
                    serverInfo["password"] = reObjMatchPwd.group(1)
                reObjMatchPort = re.search("<h4>端口:(\d+)</h4>".format(serverType), lineString)
                if reObjMatchPort:
                    breakPointIndex += 1
                    if "{0}-{1}".format(serverType, breakPointIndex) == "A-1":
                        serverInfo["port"] = reObjMatchPort.group(1)
                    elif "{0}-{1}".format(serverType, breakPointIndex) == "B-2":
                        serverInfo["port"] = reObjMatchPort.group(1)
                    elif "{0}-{1}".format(serverType, breakPointIndex) == "C-3":
                        serverInfo["port"] = reObjMatchPort.group(1)
                    else:
                        continue
        return serverInfo
    except urllib.error.URLError as error:
        logMsg(error)

def mergeLocalConfig(serverInfo):
    " merge ishadowsocks website server information to local ishadowsocks client configure file"
    # TODO get local configure file and merge it
    guiConfigFileHandle = open("gui-config.json", encoding="utf-8")
    guiConfigJsonObj = json.load(guiConfigFileHandle)

    guiOldValue = guiConfigJsonObj['configs'][0]
    # assign new server information
    guiConfigJsonObj['configs'][0]["server"] = serverInfo['server']
    guiConfigJsonObj['configs'][0]["password"] = serverInfo['password']
    guiConfigJsonObj['configs'][0]["server_port"] = serverInfo['port']

    guiNewConfigFileHandle = open("gui-config.json", encoding="utf-8", mode="w")
    json.dump(guiConfigJsonObj, guiNewConfigFileHandle)
    logMsg("replace old server value [{0}] to new value [{1}].".format(str(guiOldValue), str(serverInfo)))
    return True

def logMsg(msg):
    "log process information to application.log"
    logging.basicConfig(filename="application.log", level=logging.INFO, format="[%(asctime)s]: %(message)s")
    logging.info(msg)

def killProcess():
    "update server information need restart, so first kill it."
    try:
        os.system("taskkill /F /IM {0} /T".format(ISHADOWSCOKS_PROCESS_NAME))
    except  OSError as error:
        logMsg(error)

def startProcess():
    "start shadowsocks process"
    try:
        os.system("start {0} &".format(ISHADOWSCOKS_PROCESS_PATH))
    except OSError as error:
        logMsg(error)


if __name__ == "__main__":
    updateServer(SERVER_TYPE)

