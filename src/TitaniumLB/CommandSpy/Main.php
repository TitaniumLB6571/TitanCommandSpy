<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Main extends PluginBase
{
    /** @var array */
    public array $snoopers = [];

    /** @var Config */
    public Config $cfg;

    /** @var array */
    public array $protectedPlayers = [];

    /** @var array */
    public array $protectedCommands = [];

    public function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array(
            "protected-players" => "Steve,Alex",
            "protected-commands" => "kick,ban",
            "Log-SpyMode-To-Console" => "true",
        ));
        $this->protectedPlayers = explode(",", $this->getConfig()->get("protected-players"));
        $this->protectedCommands = explode(",", $this->getConfig()->get("protected-commands"));
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
            } else {
                if ($this->cfg->get("Log-SpyMode-To-Console") == "false") {
                    $sender->sendMessage("Set 'Log-SpyMode-To-Console' to 'true' to enable spy-mode in console");
                } else {
                    $sender->sendMessage("Spy-mode is already active.");
                }
            }
        }
        return true;
    }
}