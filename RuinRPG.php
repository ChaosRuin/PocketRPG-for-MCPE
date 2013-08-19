<?php

/*
__PocketMine Plugin__
name=RuinRPG
description=The best rpg for mcpe
version=0.0.1
author=ChaosRuin(alchemistdy@naver.com)
class=RuinRPG
apiversion=9
*/

class RuinRPG implements Plugin{

	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function __destruct(){}
	private function overwriteConfig($dat){
			$cfg = array();
			$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
			$result = array_merge($cfg, $dat);
			$this->api->plugin->writeYAML($this->path."config.yml", $result);
		}	
	public function init(){
		$this->api->addHandler("player.join", array($this, "Handler"), 5);
		$this->api->addHandler("player.quit", array($this, "Handler"), 5);
		$this->api->addHandler("player.spawn", array($this, "Handler"), 6);
		$this->api->console->register("시작", "<성별/종족> <남자,여자/인간,엘프,드워프>", array($this, "defaultCommands"));
		$this->api->ban->cmdWhitelist("시작");		
		$this->readConfig();
	}
	
	public function readConfig(){
		$this->path = $this->api->plugin->createConfig($this, array(
			"[RuinRPG설정]" => array(
				"사용" => false,
			),
		));
		if(is_dir("./plugins/RuinRPG/") === false){
			mkdir("./plugins/RuinRPG/");
		}
		if(is_dir("./plugins/RuinRPG/player/") === false){
			mkdir("./plugins/RuinRPG/player/");
		}
	}
	
	public function Handler(&$data, $event){
		switch($event){
			case "player.spawn":
					if($this->data[$data->username]->get("race") === 0 or $this->data[$data->username]->get("gender") === 0){
					$data->sendChat("[RuinRPG] 성별과 종족을 선택해주세요\n/시작 <성별/종족> <남자,여자/인간,엘프,드워프>\n");
						break;
					}else{
					$data->sendChat("[RuinRPG] 이 서버는 RuinRPG플러그인을 사용합니다.\n#버젼: 1.0\n#제작: 카오스루인(alchemistdy@naver.com)");
					}
					break;
			case "player.join":
					$this->data[$data->username] = new Config(DATA_PATH."/plugins/RuinRPG/player/".$data->username.".yml", CONFIG_YAML, array(
							'name' => $data->username,//이름
							'level' => "1",//레벨
							'race' => "0",//종족(0:-, 1:인간, 2:엘프, 3:드워프)
							'gender' => "0",//성별(0:고자, 1:남자, 2:여자)
							'job' => "0",//전직(0:초보자, 1:검사(가죽갑빠), 2:(사슬갑빠), 3:(철갑빠), 4:(다야갑빠))
							'money' => "1000",//소지금(기본자금)
						));
				break;
			case "player.quit":
				if($this->data[$data->username] instanceof Config){
					$this->data[$data->username]->save();
				}
				break;
				}
			}
			
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		$cfg = $this->data;
		switch($cmd){
			case "시작":
				switch($params[0]){
			default:
					$output .= "사용법:/시작 <성별/종족> <남자,여자/인간,엘프,드워프>\n";
					break;
			case "":
					$output .= "사용법:/시작 <성별/종족> <남자,여자/인간,엘프,드워프>\n";
					break;
			case "성별":
				switch($params[1]){
			default:
				$output .="[RuinRPG] 잘못된 선택입니다\n";
				break;
			case "":
					$output .= "사용법:/시작 성별  <남자,여자>\n";
					break;
			case "여자":
				if($cfg[$issuer->username]->get("gender") !== 0){
					$output .= "[RuinRPG] 이미 성별을 선택하셨습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("gender", 2);
					$output  .= "[RuinRPG] 성별로 여자를 선택하셨습니다.\n";
					break;
				}
				break;
			case "남자":
				if($cfg[$issuer->username]->get("gender") !== 0){
					$output .= "[RuinRPG] 이미 성별을 선택하셨습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("gender", 1);
					$output  .= "[RuinRPG] 성별로 남자를 선택하셨습니다.\n";
					break;
				}
					break;
				}
					break;
			case "종족":
				switch($params[1]){
			default:
					$output .= "[RuinRPG] 잘못된 선택입니다\n";
					break;
			case "":
					$output .= "사용법:/시작 종족 <인간/엘프/드워프>\n";
					break;
			case "인간":
				if($cfg[$issuer->username]->get("race") !== 0){
					$output .= "[RuinRPG] 이미 종족을 선택하셨습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("race", 1);
					$output  .= "[RuinRPG] 종족으로 인간을 선택하셨습니다.\n";
					break;
				}
				break;
			case "엘프":
				if($cfg[$issuer->username]->get("race") !== 0){
					$output .= "[RuinRPG] 이미 종족을 선택하셨습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("race", 2);
					$output  .= "[RuinRPG] 종족으로 엘프를 선택하셨습니다.\n";
					break;
				}
				break;
			case "드워프":
				if($cfg[$issuer->username]->get("race") !== 0){
					$output .= "[RuinRPG] 이미 종족을 선택하셨습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("race", 3);
					$output  .= "[RuinRPG] 종족으로 드워프를 선택하셨습니다.\n";
					break;
				}
				break;
				}
					break;
				}	
					break;
			}
			 return $output;
			}
		}
