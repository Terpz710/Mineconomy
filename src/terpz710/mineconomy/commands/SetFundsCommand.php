<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Error;
use terpz710\mineconomy\utils\Message;

class SetFundsCommand extends Command {

    public function __construct() {
        parent::__construct("setfunds");
        $this->setDescription("Set a player's account balance");
        $this->setUsage("Usage: /setfunds <player> <amount>");
        $this->setPermission("mineconomy.setfunds");
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

        if ($amount < 0) {
            $sender->sendMessage("The amount must be 0 or greater!");
            return false;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($targetName);

        if ($targetPlayer instanceof Player) {
            Mineconomy::getInstance()->setFunds($targetPlayer, $amount);
            $sender->sendMessage("Set {$targetPlayer->getName()}'s account balance to $amount!");
            $targetPlayer->sendMessage("Your account balance has been set to $amount!");
        } else {
            $economy = Mineconomy::getInstance();

            if ($economy->hasBalance($targetName)) {
                $economy->setFunds($targetName, $amount);
                $sender->sendMessage("Set $targetName's account balance to $amount!");
            } else {
                $sender->sendMessage("$targetName does not have an account!");
            }
        }
        return true;
    }
}