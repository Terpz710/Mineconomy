<?php

declare(strict_types=1);

namespace terpz710\mineconomy\event;

use pocketmine\event\Event;

use pocketmine\player\Player;

class BalanceChangeEvent extends Event {

    private Player|string $player;
    private int $oldBalance;
    private int $newBalance;
    private string $changeType;

    public function __construct(Player|string $player, int $oldBalance, int $newBalance, string $changeType) {

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