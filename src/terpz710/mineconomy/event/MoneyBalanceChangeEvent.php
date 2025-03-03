<?php

declare(strict_types=1);

namespace terpz710\mineconomy\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

class MoneyBalanceChangeEvent extends Event {

    public function __construct(
        protected Player|string $player, 
        protected int $oldBalance, 
        protected int $newBalance, 
        protected string $changeType
    ) {
        $this->player = $player;
        $this->oldBalance = $oldBalance;
        $this->newBalance = $newBalance;
        $this->changeType = $changeType;
    }

    public function getPlayer() : Player|string{
        return $this->player;
    }

    public function getOldBalance() : int{
        return $this->oldBalance;
    }

    public function getNewBalance() : int{
        return $this->newBalance;
    }

    public function getChangeType() : string{
        return $this->changeType;
    }
}