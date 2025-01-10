<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api\database;

use mysqli;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\api\EconomyInterface;

use terpz710\mineconomy\event\BalanceChangeEvent;

final class EconomyMySQL implements EconomyInterface {

    private function getConnection() : mysqli{
        $config = Mineconomy::getInstance()->getConfig();
        $host = $config->get("mysql-host");
        $user = $config->get("mysql-user");
        $password = $config->get("mysql-password");
        $database = $config->get("mysql-database");

        $connection = new mysqli($host, $user, $password, $database);

        if ($connection->connect_error) {
            throw new \RuntimeException("Failed to connect to MySQL: " . $connection->connect_error);
        }

        return $connection;
    }

    private function getUUID(Player|string $player) : ?string{
        if ($player instanceof Player) {
            return $player->getUniqueId()->toString();
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT uuid FROM economy WHERE username = ?");
        $stmt->bind_param("s", $player);
        $stmt->execute();
        $result = $stmt->get_result();
        $uuid = $result->fetch_assoc()["uuid"] ?? null;
        $stmt->close();
        $connection->close();

        return $uuid;
    }

    public function createBalance(Player|string $player) : void{
        if ($player instanceof Player) {
            $uuid = $player->getUniqueId()->toString();
            $username = $player->getName();
        } else {
            return;
        }

        if (!$this->hasBalance($player)) {
            $connection = $this->getConnection();
            $startingAmount = Mineconomy::getInstance()->getConfig()->get("starting-amount");
            $stmt = $connection->prepare("INSERT INTO economy (uuid, username, balance) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $uuid, $username, $startingAmount);
            $stmt->execute();
            $stmt->close();
            $connection->close();
        }
    }

    public function hasBalance(Player|string $player) : bool{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return false;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT 1 FROM economy WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        $connection->close();

        return $exists;
    }

    public function getBalance(Player|string $player) : ?int{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return null;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("SELECT balance FROM economy WHERE uuid = ?");
        $stmt->bind_param("s", $uuid);
        $stmt->execute();
        $result = $stmt->get_result();
        $balance = $result->fetch_assoc()["balance"] ?? null;
        $stmt->close();
        $connection->close();

        return $balance;
    }

    public function addFunds(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE economy SET balance = balance + ? WHERE uuid = ?");
        $stmt->bind_param("is", $amount, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        $newBalance = $this->getBalance($player);
        if ($newBalance !== null) {
            (new BalanceChangeEvent($player, $newBalance, $amount, "add"))->call();
        }
    }

    public function removeFunds(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE economy SET balance = balance - ? WHERE uuid = ?");
        $stmt->bind_param("is", $amount, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        $newBalance = $this->getBalance($player);
        if ($newBalance !== null) {
            (new BalanceChangeEvent($player, $newBalance, $amount, "remove"))->call();
        }
    }

    public function setFunds(Player|string $player, int $amount) : void{
        $uuid = $this->getUUID($player);
        if ($uuid === null) {
            return;
        }

        $connection = $this->getConnection();
        $stmt = $connection->prepare("UPDATE economy SET balance = ? WHERE uuid = ?");
        $stmt->bind_param("is", $amount, $uuid);
        $stmt->execute();
        $stmt->close();
        $connection->close();

        (new BalanceChangeEvent($player, $amount, 0, "set"))->call();
    }
}