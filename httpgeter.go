package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"os/exec"
	"regexp"
	"strconv"
)

type GuiConfigStruct struct {
	Configs                []ServerItemStruct `json:"configs"`
	Strategy               *bool              `json:"strategy"`
	Index                  int                `json:"index"`
	Global                 bool               `json:"global"`
	Enabled                bool               `json:"enabled"`
	ShareOverLan           bool               `json:"shareOverlan"`
	IsDefault              bool               `json:"isDefault"`
	LocalPort              int                `json:"localPort"`
	PacUrl                 *string            `json:"pacUrl"`
	UseOnlinePac           bool               `json:"useOnlinePac"`
	AvailabilityStatistics bool               `json:"availabilityStatistics"`
}

type ServerItemStruct struct {
	Server      string `json:"server"`
	Server_port int    `json:"server_port"`
	Password    string `json:"password"`
	Method      string `json:"method"`
	Remarks     string `json:"remarks"`
}

type RemoteHostPortPassword struct {
	Host     string
	Port     int
	Password string
}

const SHADOWSOCKS_URL = "http://www.ishadowsocks.org/"

func main() {
	//stopShadowsocks()
	var contents string = httpGetUrl(SHADOWSOCKS_URL)
	remoteHostPortPassword := matchHostAndPassword(contents)
	fmt.Println(remoteHostPortPassword)
	defaultConfig := getGuiConfig()
	rebuildConfig(&remoteHostPortPassword, &defaultConfig)
	jsonBytes, err := json.Marshal(&defaultConfig)
	if err != nil {
		fmt.Println(err)
		return
	}
	ioutil.WriteFile("gui-config.json", jsonBytes, 0777)
	startShadowsocks()
}

func stopShadowsocks() {
	command := exec.Command("taskkill /F /IM shadowsocks.exe")
	if err := command.Run(); err != nil {
		fmt.Println(err)
	}
}

func startShadowsocks() {
	command := exec.Command("cmd", "/C", "shadowsocks.exe")
	if err := command.Run(); err != nil {
		fmt.Println(err)
	}
}

func rebuildConfig(remoteHostPortPassword *RemoteHostPortPassword, guiConfig *GuiConfigStruct) {
	guiConfig.Configs[0].Server = remoteHostPortPassword.Host
	guiConfig.Configs[0].Server_port = remoteHostPortPassword.Port
	guiConfig.Configs[0].Password = remoteHostPortPassword.Password
}

func httpGetUrl(url string) string {

	response, error := http.Get(url)
	if error != nil {
		log.Fatal(error)
	}

	contents, error := ioutil.ReadAll(response.Body)
	response.Body.Close()

	if error != nil {
		log.Fatal(error)
	}
	return string(contents)
}

func matchHostAndPassword(body string) RemoteHostPortPassword {
	regexObj := regexp.MustCompile("C服务器地址:(?P<ip>\\w*\\.\\w*\\.\\w*).*\\n.*端口:(?P<port>\\d*).*\\n.*C密码:(?P<pwd>\\d*)")
	matchArray := regexObj.FindStringSubmatch(body)

	var matchLength int = len(matchArray)
	resultSet := new(RemoteHostPortPassword)
	if matchLength == 4 {
		resultSet.Host = matchArray[1]
		intPort, err := strconv.Atoi(matchArray[2])
		if err != nil {
			fmt.Println(err)
		}
		resultSet.Port = intPort
		resultSet.Password = matchArray[3]
	}

	return *resultSet
}

func getGuiConfig() GuiConfigStruct {

	/*
		c, error := ioutil.ReadFile("gui-config.json")

		if error != nil {
			log.Fatal(error)
		}
	*/

	var defaultConfig = GuiConfigStruct{
		Strategy:               nil,
		Index:                  0,
		Global:                 false,
		Enabled:                true,
		ShareOverLan:           false,
		IsDefault:              false,
		LocalPort:              1080,
		PacUrl:                 nil,
		UseOnlinePac:           false,
		AvailabilityStatistics: false,
	}
	defaultConfig.Configs = make([]ServerItemStruct, 1)
	defaultConfig.Configs[0] = ServerItemStruct{
		Server:      "jp3.iss.tf",
		Server_port: 10000,
		Password:    "luowen",
		Method:      "aes-256-cfb",
		Remarks:     "",
	}
	return defaultConfig
}
