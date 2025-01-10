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

class PayFundsCommand extends Command {

    public function __construct() {
        parent::__construct("payfunds");
        $this->setDescription("Pay funds to another player's account");
        $this->setUsage("Usage: /payfunds <player> <amount>");
        $this->setPermission("mineconomy.payfunds");
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

        $economy = Mineconomy::getInstance();

        if (!$economy->hasBalance($sender) || $economy->getBalance($sender) < $amount) {
            $sender->sendMessage("You do not have enough funds to make this payment!");
            return false;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($targetName);

        if ($targetPlayer instanceof Player) {
            $economy->removeFunds($sender, $amount);
            $economy->addFunds($targetPlayer, $amount);
            $sender->sendMessage("You paid $amount funds to {$targetPlayer->getName()}!");
            $targetPlayer->sendMessage("You have received $amount funds from {$sender->getName()}!");
        } else {
            if ($economy->hasBalance($targetName)) {
                $economy->removeFunds($sender, $amount);
                $economy->addFunds($targetName, $amount);
                $sender->sendMessage("You paid $amount funds to $targetName!");
            } else {
                $sender->sendMessage("$targetName does not have an account!");
            }
        }

        return true;
    }
}