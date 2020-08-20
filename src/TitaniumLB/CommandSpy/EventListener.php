<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use TitaniumLB\CommandSpy\Main;

class EventListener implements Listener {
    public $plugin;
	
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	public function getPlugin() {
		return $this->plugin;
	}
	
	public function onPlayerCmd(PlayerCommandPreprocessEvent $event) {
		$sender = $event->getPlayer();
		$msg = $event->getMessage();
		
		if($this->getPlugin()->cfg->get("Log-SpyMode-To-Console") == "true") {
			if($msg[0] == "/") {
				if(stripos($msg, "login") || stripos($msg, "log") || stripos($msg, "reg") || stripos($msg, "register")) {
					$this->getPlugin()->getLogger()->info($sender->getName() . "> hidden for security reasons");	
				} else {
					$this->getPlugin()->getLogger()->info("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §f". $msg);
				}
				
			}
		}
			
			if(!empty($this->getPlugin()->snoopers)) {
				foreach($this->getPlugin()->snoopers as $snooper) {
					 if($msg[0] == "/") {
                         if (stripos($msg, "login") || stripos($msg, "log") || stripos($msg, "reg") || stripos($msg, "register")) {
                             $snooper->sendMessage($sender->getName() . "> hidden for security reasons");
                         } else {
                             $snooper->sendMessage("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §f" . $msg);
                         }

                     }
	     			}		
     			}
   		}
	}
