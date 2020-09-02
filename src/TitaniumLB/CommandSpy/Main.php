<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Main extends PluginBase
{
    public $snoopers = [];

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array(
            "Log-SpyMode-To-Console" => "true",
        ));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if (strtolower($command->getName()) == "commandspy" or strtolower($command->getName()) == "cspy") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("commandspy.cmd")) {
                    if (!isset($this->snoopers[$sender->getName()])) {
                        $sender->sendMessage("§aSpy-Mode activated.");
                        $this->snoopers[$sender->getName()] = $sender;
                        return true;
                    } else {
                        $sender->sendMessage("§cSpy-mode deactivated.");
                        unset($this->snoopers[$sender->getName()]);
                        return true;
                    }
                } else {
                    $sender->sendMessage("§cYou don't have permission to use this command!");
                    return true;
                }
            }
        }
        return true;
    }
}