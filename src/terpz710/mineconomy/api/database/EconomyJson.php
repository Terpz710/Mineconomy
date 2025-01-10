<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api\database;

use pocketmine\player\Player;

use pocketmine\utils\Config;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\api\EconomyInterface;

use terpz710\mineconomy\event\BalanceChangeEvent;

final class EconomyJson implements EconomyInterface {

    private Config $data;

    public function __construct() {
        $this->data = new Config(Mineconomy::getInstance()->getDataFolder() . "balances.json");
    }

    private function getUUID(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        foreach ($this->data->getAll() as $uuid => $record) {
            if (strcasecmp($record["username"], $player) === 0) {
                return $uuid;
            }
        }

        return null;
    }

    public function createBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            if (!$this->hasBalance($player)) {
                $this->data->set($uuid, [
                    "username" => $player->getName(),
                    "balance" => Mineconomy::getInstance()->getConfig()->get("starting-amount")
                ]);
                $this->data->save();
            }
        }
    }

    public function hasBalance(Player|string $player) : bool{
        $uuid = $this->getUUID($player);
        return $uuid !== null && $this->data->exists($uuid);
    }

    public function getBalance(Player|string $player) : ?int{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return null;
        }

        $data = $this->data->get($uuid, null);
        return $data ? $data["balance"] : null;
    }

    public function addFunds(Player|string $player, int $amount): void {
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        $oldBalance = $data["balance"];
        $newBalance = $oldBalance + $amount;

        $data["balance"] = $newBalance;
        $this->data->set($uuid, $data);
        $this->data->save();

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "add");
        $event->call();
    }

    public function removeFunds(Player|string $player, int $amount): void {
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        $oldBalance = $data["balance"];
        $newBalance = $oldBalance - $amount;

        $data["balance"] = $newBalance;
        $this->data->set($uuid, $data);
        $this->data->save();

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "remove");
        $event->call();
    }

    public function setFunds(Player|string $player, int $amount): void {
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $data = $this->data->get($uuid);
        $oldBalance = $data["balance"];
        $newBalance = $amount;

        $data["balance"] = $newBalance;
        $this->data->set($uuid, $data);
        $this->data->save();

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "set");
        $event->call();
    }
}