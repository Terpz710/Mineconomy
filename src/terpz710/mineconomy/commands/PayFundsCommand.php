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

class PayFundsCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("mineconomy.cmd");

        $this->registerArgument(0, new RawStringArgument("recipient"));
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

        $recipientName = $args["recipient"];
        $amount = $args["amount"];

        if ($amount <= 0) {
            $sender->sendMessage((string) new Message("paid-funds-cannot-be-negative"));
            return;
        }

        $plugin = Mineconomy::getInstance();
        $senderName = $sender->getName();

        if (!$plugin->hasBalance($recipientName)) {
            $sender->sendMessage((string) new Message("no-balance", ["{name}"], [$recipientName]));
            return;
        }

        $senderBalance = $plugin->getFunds($sender);
        if ($senderBalance < $amount) {
            $sender->sendMessage((string) new Message("not-enough-funds", ["{amount}"], [number_format($amount)]));
            return;
        }

        $plugin->removeFunds($sender, $amount);
        $plugin->addFunds($recipientName, $amount);

        $sender->sendMessage((string) new Message("successfully-paid-reciever", ["{recipient}", "{amount}"], [$recipientName, number_format($amount)]));
        
        $recipient = $plugin->getServer()->getPlayerByPrefix($recipientName);
        if ($recipient instanceof Player) {
            $recipient->sendMessage((string) new Message("successfully-recieved-payment", ["{name}", "amount"], [$senderName, number_format($amount)]));
        }
    }
}