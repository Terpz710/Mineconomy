<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\event\MoneyBalanceChangeEvent;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class MineconomyAPI {
    
    protected DataConnector $database;
    
    protected array $moneyCache = [];

    public function __construct(protected Mineconomy $plugin) {
        $this->plugin = $plugin;
    }

    public function init(){
        $this->database = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->database->executeGeneric("table.money");
    }

    public function loadPlayerBalance(Player $player){
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();

        $this->database->executeSelect("money.get", ["uuid" => $uuid], function(array $rows) use ($uuid, $name) {
            $balance = !empty($rows) ? (int) $rows[0]["balance"] : 0;
            $this->moneyCache[$uuid] = ["balance" => $balance, "name" => $name];
        });
    }

    public function createBalance(Player $player){
        $uuid = $player->getUniqueId()->toString();
        $name = $player->getName();
        $startingAmount = $this->plugin->getConfig()->get("starting-amount");

        if ($this->hasBalance($player)) {
            return;
        }

        $this->moneyCache[$uuid] = ["balance" => $startingAmount, "name" => $name];

        $this->database->executeChange("money.create", [
            "uuid" => $uuid,
            "name" => $name,
            "balance" => $startingAmount
        ]);
    }

    public function hasBalance(Player|string $player) : bool{
        $uuid = $this->resolveUuid($player);
        return $uuid !== '' && isset($this->moneyCache[$uuid]);
    }

    public function getFunds(Player|string $player) : int{
        $uuid = $this->resolveUuid($player);
        return $uuid !== '' ? ($this->moneyCache[$uuid]['balance'] ?? 0) : 0;
    }

    public function addFunds(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getFunds($player);
        $newBalance = $oldBalance + $amount;

        $this->moneyCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("money.add", ["uuid" => $uuid, "amount" => $amount]);

        $event = new MoneyBalanceChangeEvent($player, $oldBalance, $newBalance, "add");
        $event->call();
    }

    public function removeFunds(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getFunds($player);
        $newBalance = max(0, $oldBalance - $amount);

        $this->moneyCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("money.remove", ["uuid" => $uuid, "amount" => $amount]);

        $event = new MoneyBalanceChangeEvent($player, $oldBalance, $newBalance, "remove");
        $event->call();
    }

    public function setFunds(Player|string $player, int $amount){
        $uuid = $this->resolveUuid($player);
        if ($uuid === '') return;

        $oldBalance = $this->getFunds($player);
        $newBalance = $amount;

        $this->moneyCache[$uuid]['balance'] = $newBalance;
        $this->database->executeChange("money.set", ["uuid" => $uuid, "amount" => $amount]);

        $event = new MoneyBalanceChangeEvent($player, $oldBalance, $newBalance, "set");
        $event->call();
    }

    public function getTopFunds(callable $callback){
        $this->database->executeSelect("money.top", [], function(array $rows) use ($callback) {
            $this->moneyCache['top'] = $rows;
            $callback($rows);
        });
    }

    private function resolveUuid(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        foreach ($this->moneyCache as $uuid => $data) {
            if (isset($data['name']) && strtolower($data['name']) === strtolower($player)) {
                return $uuid;
            }
        }

        $result = null;
        $this->database->executeSelect("money.get_by_name", ["name" => $player], function(array $rows) use (&$result) {
            $result = !empty($rows) ? $rows[0]["uuid"] : null;
        });

        return $result;
    }

    public function close(){
        $this->database->close();
    }
}