<?php

declare(strict_types=1);

namespace terpz710\mineconomy;

use pocketmine\plugin\PluginBase;

use pocketmine\player\Player;

//use pocketmine\utils\Config;

use terpz710\mineconomy\commands\AddFundsCommand;
use terpz710\mineconomy\commands\RemoveFundsCommand;
use terpz710\mineconomy\commands\SetFundsCommand;
use terpz710\mineconomy\commands\PayFundsCommand;
use terpz710\mineconomy\commands\SeeFundsCommand;
use terpz710\mineconomy\commands\MyFundsCommand;

use terpz710\mineconomy\api\EconomyManager;

use terpz710\mineconomy\event\EventListener;

final class Mineconomy extends PluginBase {

    protected static self $instance;

    private EconomyManager $ecoManager;

    private Tag $scorehud;

    //public static Config $messages;

    protected Economy $eco; 

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        //$this->saveResource("messages.yml");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->registerAll("Mineconomy", [
            new AddFundsCommand(),
            new RemoveFundsCommand(),
            new SetFundsCommand(),
            new PayFundsCommand(),
            new SeeFundsCommand(),
            new MyFundsCommand()
        ]);

        $this->ecoManager = new EconomyManager();

        //self::$messages = new Config($this->getDataFolder() . "messages.yml");
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    protected function getEconomyManager() {
        return $this->ecoManager->getHandler();
    }

    public function createBalance(Player|string $player) : void{
        $this->getEconomyManager()->createBalance($player);
    }

    public function hasBalance(Player|string $player) : bool{
        return $this->getEconomyManager()->hasBalance($player);
    }

    public function getBalance(Player|string $player) : ?int{
        return $this->getEconomyManager()->getBalance($player);
    }

    public function addFunds(Player|string $player, int $amount) : void{
        $this->getEconomyManager()->addFunds($player, $amount);
    }

    public function removeFunds(Player|string $player, int $amount) : void{
        $this->getEconomyManager()->removeFunds($player, $amount);
    }

    public function setFunds(Player|string $player, int $amount) : void{
        $this->getEconomyManager()->setFunds($player, $amount);
    }
}