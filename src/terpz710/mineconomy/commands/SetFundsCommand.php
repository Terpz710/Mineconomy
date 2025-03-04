<?php

declare(strict_types=1);

namespace terpz710\mineconomy\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\utils\Message;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\IntegerArgument;

class SetFundsCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setPermission("mineconomy.cmd.op");

        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new IntegerArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage((string) new Message("use-command-ingame"));
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $targetName = $args["player"];
        $amount = $args["amount"];

        if ($amount < 0) {
            $sender->sendMessage((string) new Message("amount-cannot-be-negative"));
            return;
        }

        $plugin = Mineconomy::getInstance();

        if ($plugin->hasBalance($targetName)) {
            $plugin->setFunds($targetName, $amount);
            $sender->sendMessage((string) new Message("successfully-set-balance", ["{name}", "{amount}"], [$targetName, number_format($amount)]));
        } else {
            $sender->sendMessage((string) new Message("no-balance", ["{name}"], [$targetName]));
        }
    }
}
