<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use TitaniumLB\CommandSpy\command\SpyCommand;

class Main extends PluginBase
{

    public const PREFIX = "§cSpy-Mode§8» §8: ";
    private array $snoopers = [];
    private array $targetedSnoopers = [];
    private array $targetValue = [];
    public Config $cfg;
    public array $protectedPlayers = [];
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

        Server::getInstance()->getCommandMap()->register("CommandSpy", new SpyCommand($this));
    }

    public function toggleGlobalSpyMode(Player $sender): void
    {
        $senderName = $sender->getName();
        if (!isset($this->snoopers[$senderName])) {
            $this->snoopers[$senderName] = true;
            $sender->sendMessage(self::PREFIX . TextFormat::GREEN . "Activated.");
        } else {
            unset($this->snoopers[$senderName]);
            $sender->sendMessage(self::PREFIX . TextFormat::RED . "De-activated.");
        }
    }

    public function toggleTargetSpyMode(Player $sender, Player $target): void
    {
        $targetName = $target->getName();
        if ($sender->hasPermission("commandspy.cmd.target")) {
            if (!$this->isSpyingOnTarget($sender, $targetName)) {
                $this->targetedSnoopers[$sender->getName()][$targetName] = true;
                $this->targetValue[$sender->getName()][$targetName] = true;
                $sender->sendMessage(self::PREFIX . TextFormat::GREEN . "Now spying on " . TextFormat::GOLD . $targetName . TextFormat::GREEN . "'s commands.");
            } else {
                unset($this->targetedSnoopers[$sender->getName()][$targetName]);
                unset($this->targetValue[$sender->getName()][$targetName]);
                $sender->sendMessage(self::PREFIX . TextFormat::GREEN . "No longer spying on " . TextFormat::GOLD . $targetName . TextFormat::GREEN . "'s commands.");
            }
        } else $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command!");
    }

    public function listSnoopers(Player $sender): void
    {
        $sender->sendMessage("§8»§6§lList of snoopers§r§8:");
        $allSnoopers = array_merge($this->snoopers, $this->targetedSnoopers);

        if (empty($allSnoopers)) {
            $sender->sendMessage("§8» §aNobody is currently snooping around.");
        } else {
            foreach ($allSnoopers as $spyName => $spyMode) {
                if (is_array($spyMode) && !empty($spyMode)) {
                    $targetList = implode(", ", array_keys($spyMode));
                    $sender->sendMessage("§8»§e " . $spyName . " §ais spying on§8: §6" . $targetList);
                } else $sender->sendMessage("§8»§e " . $spyName . " §ais in §6global spy mode§a.");
            }
        }
    }

    public function isSpying(Player $sender): bool
    {
        if (in_array($sender->getName(), $this->getSnoopers()) === true) {
            return true;
        } else return false;
    }

    public function isSpyingOnTarget(Player $sender, string $targetName): bool
    {
        if (isset($this->targetedSnoopers[$sender->getName()]) && isset($this->targetedSnoopers[$sender->getName()][$targetName])) {
            return true;
        } else return false;
    }

    public function isSpyingOnTargetValue(Player $sender): bool
    {
        if (isset($this->targetValue[$sender->getName()]) && $this->targetValue[$sender->getName()]) {
            return true;
        } else return false;
    }

    public function getSnoopers(): array
    {
        return $this->snoopers;
    }

    public function getTargetValue(): array
    {
        return $this->targetValue;
    }

    public function getProtectedPlayers(): array
    {
        return $this->protectedPlayers;
    }

    public function getProtectedCommands(): array
    {
        return $this->protectedCommands;
    }
}