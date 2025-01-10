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

class RemoveFundsCommand extends Command {

    public function __construct() {
        parent::__construct("removefunds");
        $this->setDescription("Remove funds from a player's account");
        $this->setUsage("Usage: /removefunds <player> <amount>");
        $this->setPermission("mineconomy.removefunds");
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
            $sender->sendMessage("The amount must be greater than 0!");
            return false;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($targetName);

        if ($targetPlayer instanceof Player) {
            if (Mineconomy::getInstance()->getBalance($targetPlayer) >= $amount) {
                Mineconomy::getInstance()->removeFunds($targetPlayer, $amount);
                $sender->sendMessage("Removed $amount funds from {$targetPlayer->getName()}'s account!");
                $targetPlayer->sendMessage("You have had $amount funds removed from your account!");
            } else {
                $sender->sendMessage("{$targetPlayer->getName()} does not have enough funds!");
            }
        } else {
            $economy = Mineconomy::getInstance();

            if ($economy->hasBalance($targetName)) {
                if ($economy->getBalance($targetName) >= $amount) {
                    $economy->removeFunds($targetName, $amount);
                    $sender->sendMessage("Removed $amount funds from $targetName's account!");
                } else {
                    $sender->sendMessage("$targetName does not have enough funds!");
                }
            } else {
                $sender->sendMessage("$targetName does not have an account!");
            }
        }
        return true;
    }
}