<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api\database;

use SQLite3;

use pocketmine\player\Player;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\api\EconomyInterface;

use terpz710\mineconomy\event\BalanceChangeEvent;

final class EconomySQLite implements EconomyInterface {

    private SQLite3 $database;

    public function __construct() {
        $dataFolder = Mineconomy::getInstance()->getDataFolder();
        $this->database = new SQLite3($dataFolder . "balances.db");

        $this->database->exec("
            CREATE TABLE IF NOT EXISTS economy (
                uuid TEXT PRIMARY KEY,
                username TEXT,
                balance INTEGER DEFAULT 0
            );
        ");
    }

    public function hasBalance(Player|string $player) : bool{
        $query = "SELECT 1 FROM economy WHERE uuid = :uuid OR LOWER(username) = LOWER(:username);";
        $statement = $this->database->prepare($query);

        if ($player instanceof Player) {
            $statement->bindValue(":uuid", $player->getUniqueId()->toString(), SQLITE3_TEXT);
            $statement->bindValue(":username", $player->getName(), SQLITE3_TEXT);
        } else {
            $statement->bindValue(":uuid", "", SQLITE3_TEXT);
            $statement->bindValue(":username", $player, SQLITE3_TEXT);
        }

        $result = $statement->execute();
        $exists = $result->fetchArray(SQLITE3_ASSOC) !== false;
        $statement->close();

        return $exists;
    }

    public function createBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();

            if (!$this->hasBalance($player)) {
                $statement = $this->database->prepare("
                    INSERT INTO economy (uuid, username, balance) 
                    VALUES (:uuid, :username, :balance);
                ");
                $statement->bindValue(":uuid", $uuid, SQLITE3_TEXT);
                $statement->bindValue(":username", $username, SQLITE3_TEXT);
                $statement->bindValue(":balance", Mineconomy::getInstance()->getConfig()->get("starting-amount"), SQLITE3_INTEGER);
                $statement->execute();
                $statement->close();
            }
        }
    }

    public function getBalance(Player|string $player) : ?int{
        $query = "SELECT balance FROM economy WHERE uuid = :uuid OR LOWER(username) = LOWER(:username);";
        $statement = $this->database->prepare($query);

        if ($player instanceof Player) {
            $statement->bindValue(":uuid", $player->getUniqueId()->toString(), SQLITE3_TEXT);
            $statement->bindValue(":username", $player->getName(), SQLITE3_TEXT);
        } else {
            $statement->bindValue(":uuid", "", SQLITE3_TEXT);
            $statement->bindValue(":username", $player, SQLITE3_TEXT);
        }

        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
        $statement->close();

        return $data ? (int)$data["balance"] : null;
    }

    public function addFunds(Player|string $player, int $amount) : void{
        $oldBalance = $this->getBalance($player) ?? 0;
        $newBalance = $oldBalance + $amount;

        $this->updateBalance($player, $newBalance);

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "add");
        $event->call();
    }

    public function removeFunds(Player|string $player, int $amount) : void{
        $oldBalance = $this->getBalance($player) ?? 0;
        $newBalance = max(0, $oldBalance - $amount);

        $this->updateBalance($player, $newBalance);

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "remove");
        $event->call();
    }

    public function setFunds(Player|string $player, int $amount) : void{
        $oldBalance = $this->getBalance($player) ?? 0;
        $newBalance = $amount;

        $this->updateBalance($player, $newBalance);

        $event = new BalanceChangeEvent($player, $oldBalance, $newBalance, "set");
        $event->call();
    }

    private function updateBalance(Player|string $player, int $newBalance) : void{
        $uuid = null;
        $username = null;

        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();
        } else {
            $username = $player;
            $uuid = $this->getUUIDByUsername($username);
        }

        if ($uuid !== null) {
            $statement = $this->database->prepare("
                UPDATE economy SET balance = :balance WHERE uuid = :uuid;
            ");
            $statement->bindValue(":balance", $newBalance, SQLITE3_INTEGER);
            $statement->bindValue(":uuid", $uuid, SQLITE3_TEXT);
            $statement->execute();
            $statement->close();
        }
    }

    private function getUUIDByUsername(string $username) : ?string{
        $statement = $this->database->prepare("SELECT uuid FROM economy WHERE LOWER(username) = LOWER(:username);");
        $statement->bindValue(":username", $username, SQLITE3_TEXT);
        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
        $statement->close();

        return $data["uuid"] ?? null;
    }
}
