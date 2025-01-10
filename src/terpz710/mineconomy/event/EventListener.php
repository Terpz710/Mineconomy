<?php

declare(strict_types=1);

namespace terpz710\mineconomy\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use Ifera\ScoreHud\ScoreHud;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;

class EventListener implements Listener {

    public function join(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $eco = Mineconomy::getInstance();

        if (!$eco->hasBalance($player)) {
            $eco->createBalance($player);
        }
    }
}