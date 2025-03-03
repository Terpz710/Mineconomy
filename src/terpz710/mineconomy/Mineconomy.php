<?php

declare(strict_types=1);

namespace terpz710\mineconomy;

use pocketmine\plugin\PluginBase;

use terpz710\mineconomy\commands\AddFundsCommand;
use terpz710\mineconomy\commands\RemoveFundsCommand;
use terpz710\mineconomy\commands\SetFundsCommand;
use terpz710\mineconomy\commands\PayFundsCommand;
use terpz710\mineconomy\commands\SeeFundsCommand;
use terpz710\mineconomy\commands\MyFundsCommand;
use terpz710\mineconomy\commands\TopFundsCommand;

use terpz710\mineconomy\api\MineconomyAPI;

use CortexPE\Commando\PacketHooker;

final class Mineconomy extends PluginBase {

    protected static self $instance;

    protected MineconomyAPI $eco;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("Mineconomy", [
            new AddFundsCommand($this, "addfunds", "Add money to a players balance"),
            new RemoveFundsCommand($this, "removefunds", "Remove money from a players balance"),
            new SetFundsCommand($this, "setfunds", "Set a players balance"),
            new PayFundsCommand($this, "pay", "Pay another player"),
            new SeeFundsCommand($this, "seefunds", "See another players balance"),
            new MyFundsCommand($this, "myfunds", "Shows your current balance"),
            new TopFundsCommand($this, "topfunds", "Show the top 10 balances")
        ]);

        $languageFolder = $this->getDataFolder() . "languages/";
        if (!is_dir($languageFolder)) {
            mkdir($languageFolder, 0777, true);
        }

        $langConfig = [
            "english_messages.yml", 
            "spanish_messages.yml", 
            "german_messages.yml", 
            "french_messages.yml",
            "traditional_chinese_messages.yml",
            "simplified_chinese_messages.yml"
        ];
        
        foreach ($langConfig as $file) {
            if (!file_exists($languageFolder . $file)) {
                $this->saveResource("languages/" . $file);
            }
        }

        $this->eco = new MineconomyAPI($this);
        $this->eco->init();
    }

    protected function onDisable() : void{
        $this->eco->close();
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function loadPlayerBalance(Player $player){
        return $this->eco->loadPlayerBalance($player);
    }

    public function createBalance(Player $player){
        return $this->eco->createBalance($player);
    }

    public function hasBalance(Player|string $player) : bool{
        return $this->eco->hasBalance($player);
    }

    public function getFunds(Player|string $player) : ?int{
        return $this->eco->getFunds($player);
    }

    public function addFunds(Player|string $player, int $amount){
        return $this->eco->addFunds($player, $amount);
    }

    public function removeFunds(Player|string $player, int $amount){
        return $this->eco->removeFunds($player, $amount);
    }

    public function setFunds(Player|string $player, int $amount){
        return $this->eco->setFunds($player, $amount);
    }

    public function getTopFunds(callable $callback){
        return $this->eco->getTopFunds($callback);
    }
}