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

class SeeFundsCommand extends Command {

    public function __construct() {
        parent::__construct("seefunds");
        $this->setDescription("View a player's account balance");
        $this->setUsage("Usage: /seefunds <player>");
        $this->setPermission("mineconomy.seefunds");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) === 0) {
            $sender->sendMessage($this->getUsage());
            return false;
        }

        $targetName = $args[0];
        $economy = Mineconomy::getInstance();
        $targetPlayer = Server::getInstance()->getPlayerByPrefix($targetName);

        if ($targetPlayer instanceof Player) {
            if ($economy->hasBalance($targetPlayer)) {
                $balance = $economy->getBalance($targetPlayer);
                $sender->sendMessage("{$targetPlayer->getName()}'s balance is $balance funds.");
            } else {
                $sender->sendMessage("{$targetPlayer->getName()} does not have an account!");
            }
        } else {
            if ($economy->hasBalance($targetName)) {
                $balance = $economy->getBalance($targetName);
                $sender->sendMessage("$targetName's balance is $balance funds.");
            } else {
                $sender->sendMessage("$targetName does not have an account!");
            }
        }

        return true;
    }
}