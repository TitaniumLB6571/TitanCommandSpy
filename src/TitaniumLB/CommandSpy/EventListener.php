<?php

namespace TitaniumLB\CommandSpy;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class EventListener implements Listener
{
    public Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Main
    {
        return $this->plugin;
    }

    public function onCommandEvent(CommandEvent $event): void
    {
        $sender = $event->getSender();
        $msg = $event->getCommand();
        $words = explode(" ", $msg);
        $cmd = strtolower(array_shift($words));

        if ($sender instanceof Player) {
            $protectedPlayers = $this->getPlugin()->getProtectedPlayers();
            $protectedCommands = $this->getPlugin()->getProtectedCommands();

            if (in_array($sender->getName(), $protectedPlayers) || in_array($cmd, $protectedCommands)) return;

            if ($sender->isConnected()) {
                foreach ($this->getPlugin()->getSnoopers() as $snooperName => $targets) {
                    $snooper = Server::getInstance()->getPlayerByPrefix($snooperName);
                    if ($snooper !== null && $this->getPlugin()->isSpying($snooper)) {
                        $this->sendSpyMessage($snooper, $sender->getName(), $msg);
                    }
                }

                foreach ($this->getPlugin()->getTargetValue() as $targetSnooperName => $targetPlayers) {
                    $targetSnooper = Server::getInstance()->getPlayerByPrefix($targetSnooperName);
                    if ($targetSnooper !== null && $this->getPlugin()->isSpyingOnTarget($targetSnooper, $sender->getName())) {
                        $this->sendSpyMessage($targetSnooper, $sender->getName(), $msg);
                    }
                }
            }
        }
        if ($this->getPlugin()->cfg->get("Log-SpyMode-To-Console") == "true") {
            if ($sender instanceof ConsoleCommandSender) {
                return;
            } else $this->getPlugin()->getLogger()->info("§cSpy-Mode§8» §8:§6" . $sender->getName() . "§8: §f/" . $msg);
        }
    }

    private function sendSpyMessage(Player $snooper, string $senderName, string $command): void
    {
        $snooper->sendMessage("§cSpy-Mode§8» §8:§6$senderName" . "§8: §f/$command");
    }
}