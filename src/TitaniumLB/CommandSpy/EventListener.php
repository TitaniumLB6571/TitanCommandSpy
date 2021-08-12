<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class EventListener implements Listener
{
    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function onPlayerCmd(PlayerCommandPreprocessEvent $event)
    {
        $sender = $event->getPlayer();
        $msg = $event->getMessage();
        $words = explode(" ", $msg);
        $cmd = strtolower(substr(array_shift($words), 1));

        if ($this->getPlugin()->cfg->get("Log-SpyMode-To-Console") == "true") {
            if ($msg[0] == "/") {
                if (stripos($msg, "login") || stripos($msg, "log") || stripos($msg, "reg") || stripos($msg, "register")) {
                    $this->getPlugin()->getLogger()->info("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §e§lENCRYPTED§r");
                } else {
                    $this->getPlugin()->getLogger()->info("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §f" . $msg);
                }
            }
        }

        if (!empty($this->getPlugin()->snoopers)) {
            foreach ($this->getPlugin()->snoopers as $snooper) {
                if ($msg[0] == "/") {
                    if (in_array($sender->getName(), $this->getPlugin()->protectedPlayers) or in_array($cmd, $this->getPlugin()->protectedCommands) == true || stripos($msg, "login") || stripos($msg, "reg") || stripos($msg, "register")) {
                        $snooper->sendMessage("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §e§lENCRYPTED§r");
                    } else {
                        $snooper->sendMessage("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §f" . $msg);
                    }
                }
            }
        }
    }
}