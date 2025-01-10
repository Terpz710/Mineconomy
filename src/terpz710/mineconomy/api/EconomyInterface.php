<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api;

use pocketmine\player\Player;

interface EconomyInterface {

    public function createBalance(Player|string $player) : void;

    public function hasBalance(Player|string $player) : bool;

    public function getBalance(Player|string $player) : ?int;

    public function addFunds(Player|string $player, int $amount) : void;

    public function removeFunds(Player|string $player, int $amount) : void;

    public function setFunds(Player|string $player, int $amount) : void;

}