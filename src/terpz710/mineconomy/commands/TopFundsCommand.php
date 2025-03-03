<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Message;

use CortexPE\Commando\BaseCommand;

class TopFundsCommand extends BaseCommand {

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

        $plugin = Mineconomy::getInstance();
        /* TODO: Configurable leaderboard message... **/
        $plugin->getTopFunds(function(array $topPlayers) use ($sender) {
            $sender->sendMessage("Top balances:");
            $rank = 1;
            foreach ($topPlayers as $player) {
                $name = $player["name"];
                $balance = $player["balance"];
                $sender->sendMessage("§7#{$rank}: §a{$name} §7- §a$balance");
                $rank++;
            }
        });
    }
}