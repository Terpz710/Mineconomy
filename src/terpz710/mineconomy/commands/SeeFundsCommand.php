<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Message;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class SeeFundsCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("mineconomy.cmd");

        $this->registerArgument(0, new RawStringArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $targetName = $args["player"];
        $plugin = Mineconomy::getInstance();

        if (!$plugin->hasBalance($targetName)) {
            $sender->sendMessage((string) new Message("no-balance", ["{name}"], [$targetName]));
            return;
        }

        $balance = $plugin->getFunds($targetName);
        $sender->sendMessage((string) new Message("player-balance", ["{name}", "{balance}"], [$targetName, number_format($balance)]));
    }
}