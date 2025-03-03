<?php

declare(strict_types=1);

namespace terpz710\mineconomy;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $eco = Mineconomy::getInstance();

        if (!$eco->hasBalance($player)) {
            $eco->createBalance($player);
        }

        $eco->loadPlayerBalance($player);
    }
}