<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Error;
use terpz710\mineconomy\utils\Message;
use terpz710\mineconomy\utils\Permission;

class AddFundsCommand extends Command implements PluginOwned {

    public function __construct() {
        parent::__construct("addfunds");
        $this->setDescription("Add funds to a player's account");
        $this->setUsage("Usage: /addfunds <player> <amount>");
        $this->setPermission(Permission::PERM_ADDFUNDS);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) < 2) {
            $sender->sendMessage($this->getUsage());
            return false;
        }

        $targetName = $args[0];
        $amount = (int) $args[1];

        if ($amount <= 0) {
            $sender->sendMessage((string) new Message("must-be-positive-amount"));
            return false;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($targetName);

        if ($targetPlayer instanceof Player) {
            Mineconomy::getInstance()->addFunds($targetPlayer, $amount);
            $sender->sendMessage("Added $amount funds to {$targetPlayer->getName()}'s account!");
            $targetPlayer->sendMessage("You have received $amount funds!");
        } else {
            $economy = Mineconomy::getInstance();

            if ($economy->hasBalance($targetName)) {
                $economy->addFunds($targetName, $amount);
                $sender->sendMessage("Added $amount funds to $targetName's account!");
            } else {
                $sender->sendMessage($targetName . " does not have an account!");
            }
        }
        return true;
    }

    public function getOwningPlugin() : Plugin{
        return Mineconomy::getInstance();
    }
}
