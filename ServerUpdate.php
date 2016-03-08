<?php
/**
 * FileName: ServerUpdate.php
 * Description: fetch http://ishadowsocks.com last password
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2016-03-08 09:31:15
 * History:
 */

$websiteUrl = 'http://www.ishadowsocks.com/'; // 网站地址, 一般不用修改
$typeServer = 'B'; // ['A', 'B', 'C'] 选用服务器地址 A表示美国, B表示香港, C表示日本
$appPath = "D:/Services/shadowsocks/Shadowsocks.exe"; // 安装目录

class ServerUpdate
{
    protected $websiteUrl = 'http://www.ishadowsocks.com/';
    protected $typeServer = 'A';
    protected $appPath = "D:/Services/shadowsocks/Shadowsocks.exe";
    protected $serverInfo = array();

    const CONFIG_FILE = "gui-config.json";
    const KILL_APP = "taskkill /F /IM Shadowsocks.exe /T";
    const START_APP = "start %s &";

    public function __construct($websiteUrl, $typeServer, $appPath)
    {
        $this -> websiteUrl = $websiteUrl;
        $this -> typeServer = $typeServer;
        $this -> appPath = $appPath;
    }

    public function run()
    {
        try
        {
            $serverInfo = $this -> getServerInfo();
            $newServerInfo = $this -> mergeConfig($serverInfo);
            $this -> dumpConfig($newServerInfo);
            $this -> killApp();
            sleep(3);
            $this -> startApp();
        }
        catch(Exception $e)
        {
            $this -> log($e -> getMessage());
        }
    }

    public function getServerInfo()
    {
        if(!empty($this -> serverInfo)) return $this -> serverInfo;

        $serverInfo = array();
        $contents = file_get_contents($this -> websiteUrl);

        $serverPattern = "/". $this -> typeServer . "服务器地址:(.*)\</";
        $serverMatches = array();
        $result = preg_match($serverPattern, $contents, $serverMatches);
        if($result && isset($serverMatches[1])) // assign server address
            $serverInfo['server'] = $serverMatches[1];

        $pwdPattern = "/". $this -> typeServer . "密码:(.*)\</";
        $pwdMatches = array();
        $result = preg_match($pwdPattern, $contents, $pwdMatches);
        if($result && isset($pwdMatches[1]))
            $serverInfo['password'] = $pwdMatches[1];

        $portPattern = "/端口:(\d+)/";
        $portMatches = array();
        $result = preg_match_all($portPattern, $contents, $portMatches);

        if($result && isset($portMatches[1]))
        {
            switch($this -> typeServer)
            {
            case 'A':
                $serverInfo['server_port'] = $portMatches[1][0];
                break;
            case 'B':
                $serverInfo['server_port'] = $portMatches[1][1];
                break;
            case 'C':
                $serverInfo['server_port'] = $portMatches[1][2];
                break;
            default:
                break;
            }
        }

        return $this -> serverInfo = $serverInfo;
    }

    protected function mergeConfig($newServerInfo)
    {
        if(empty($newServerInfo))
        {
            $this -> log($message = 'Get server infomation from shadowsocks.com error.');
            throw new Exception($message);
        }
        $oldServerInfo = $this -> getConfigure();
        $oldServerInfo['configs'][0] = array_merge($oldServerInfo['configs'][0], $newServerInfo);
        return $oldServerInfo;
    }

    protected function getConfigure()
    {
        $softConfigString = file_get_contents(self::CONFIG_FILE);
        $softConfigJson = json_decode($softConfigString, true);
        if(is_array($softConfigJson) && isset($softConfigJson['configs']) && isset($softConfigJson['configs'][0]))
            return $softConfigJson;
        $this -> log($message = 'Application config file[gui-config.json] bad format. please check it.');
        throw new Exception($message);
    }

    protected function dumpConfig($config)
    {
        file_put_contents(self::CONFIG_FILE, json_encode($config, JSON_UNESCAPED_UNICODE));
    }

    protected function killApp()
    {
        echo exec(self::KILL_APP);
    }

    protected function startApp()
    {
        $startCommand = sprintf(self::START_APP, $this -> appPath);
        echo exec($startCommand);
    }

    protected function log($msg)
    {
        $formatMessage = '['. date('Y-m-d H:i:s', time()).']: ' . $msg . "\n";
        $fileHandle = file_put_contents('Application.log', $formatMessage, FILE_APPEND);
    }

}
(new ServerUpdate($websiteUrl, $typeServer, $appPath)) -> run();
