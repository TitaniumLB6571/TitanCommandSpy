<?php

namespace TitaniumLB\CommandSpy\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;
use TitaniumLB\CommandSpy\Main;

class SpyCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct(Main $plugin)
    {
        $this->owningPlugin = $plugin;
        parent::__construct("commandspy", "Enter Spy-Mode", '/cspy <list|player>', ["cspy"]);
        $this->setPermission("commandspy.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender->hasPermission("commandspy.cmd")) {
            if ($sender instanceof Player) {
                if (isset($args[0]) && $args[0] == "list") {
                    if (!$sender->hasPermission("commandspy.cmd.list")) {
                        $sender->sendMessage("§cYou don't have permission to use this command!");
                    } else $this->getOwningPlugin()->listSnoopers($sender);
                } else {
                    if (empty($args[0])) {
                        if ($this->getOwningPlugin()->isSpyingOnTargetValue($sender)) {
                            $sender->sendMessage(Main::PREFIX . "§cDisable targeted spy-mode §6(/cspy <playerName>) §cbefore using this command.");
                        } else $this->getOwningPlugin()->toggleGlobalSpyMode($sender);
                    } else {
                        $target = Server::getInstance()->getPlayerByPrefix($args[0]);
                        if ($target === null) {
                            $sender->sendMessage(Main::PREFIX . "§cThe player §6" . $args[0] . " §cis not online.");
                            return;
                        }
                        if ($target->getName() === $sender->getName()) {
                            $sender->sendMessage(Main::PREFIX . "§cYou can't spy on yourself.");
                            return;
                        }
                        if ($this->getOwningPlugin()->isSpying($sender)) {
                            $sender->sendMessage(Main::PREFIX . "§cDisable global spy-mode §6(/cspy) §cbefore using this command.");
                        } else $this->getOwningPlugin()->toggleTargetSpyMode($sender, $target);
                    }
                }
            } else $sender->sendMessage("This command can only be used in-game!");
        } else $sender->sendMessage("§cYou don't have permission to use this command!");
    }
}