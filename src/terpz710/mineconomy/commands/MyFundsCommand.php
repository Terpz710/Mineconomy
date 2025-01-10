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

class MyFundsCommand extends Command {

    public function __construct() {
        parent::__construct("myfunds");
        $this->setDescription("Check your balance");
        $this->setPermission("mineconomy.myfunds");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if (!$sender instanceof Player) {
            $sender->sendMessage(Error::TYPE_USE_COMMAND_INGAME);
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $balance = Mineconomy::getInstance()->getBalance($sender);
        $sender->sendMessage("Your balance: " . $balance);
        return true;
    }
}