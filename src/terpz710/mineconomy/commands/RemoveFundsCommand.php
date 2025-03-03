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

class RemoveFundsCommand extends BaseCommand {

    protected function prepare() : void{
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

        if ($amount <= 0) {
            $sender->sendMessage((string) new Message("amount-cannot-be-negative"));
            return;
        }

        $plugin = Mineconomy::getInstance();

        if ($plugin->hasBalance($targetName)) {
            $currentBalance = $plugin->getFunds($targetName);

            if ($currentBalance < $amount) {
                $sender->sendMessage((string) new Message("not-enough-funds-to-remove", ["{name}", "{balance}", "{amount}"], [$targetName, number_format($currentBalance), number_format($amount)]));
                return;
            }

            $plugin->removeFunds($targetName, $amount);
            $sender->sendMessage((string) new Message("successfully-removed-funds", ["{name}", "{amount}"], [$targetName, number_format($amount)]));
        } else {
            $sender->sendMessage((string) new Message("no-balance", ["{name}"], [$targetName]));
        }
    }
}