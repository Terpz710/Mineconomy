<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Message;

use CortexPE\Commando\BaseCommand;

class MyFundsCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("mineconomy.cmd");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $balance = Mineconomy::getInstance()->getFunds($sender);
        $sender->sendMessage((string) new Message("your-balance", ["{balance}"], [number_format($balance)]));
    }
}