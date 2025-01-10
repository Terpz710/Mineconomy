<?php

declare(strict_types=1);

namespace terpz710\mineconomy\api;

use terpz710\mineconomy\Mineconomy;

use terpz710\mineconomy\api\database\EconomyJson;
use terpz710\mineconomy\api\database\EconomyMySQL;
use terpz710\mineconomy\api\database\EconomySQLite;

final class EconomyManager {

    private EconomyInterface $handler;

    public function __construct() {
        $databaseType = Mineconomy::getInstance()->getConfig()->get("database-type");

        if ($databaseType === "json") {
            $this->handler = new EconomyJSON();
        } elseif ($databaseType === "mysql") {
            $this->handler = new EconomyMySQL();
        } elseif ($databaseType === "sqlite") {
            $this->handler = new EconomySQLite();
        } else {
            throw new \InvalidArgumentException("Invalid database type specified in the configuration: " . $databaseType);
        }
    }

    public function getHandler() : EconomyInterface{
        return $this->handler;
    }
}