# Mineconomy  

**Mineconomy** is a lightweight economy plugin for [PocketMine-MP](https://pmmp.io) servers. It provides simple yet powerful economic features to manage player balances seamlessly!

How to pronounce **Mineconomy**: Mine-conony.

The name is a mix of Minecraft and Economy!

# Features  
- View balances for yourself or other players.  
- Add, remove, or set player balances easily.  
- Supports 2 storage options:
  - MySQL  
  - SQLite  
- Fully customizable messages and commands to suit your server's needs.
- Multi-Language support.
- Simple configuration for quick setup.

# Installation  
1. Download the latest release of **Mineconomy**.  
2. Place the downloaded `.phar` file in the `plugins` folder of your PocketMine-MP server.  
3. Restart your server.

# API

**Initiate the instance:**
```php
/* Import this class **/
use terpz710\mineconomy\Mineconomy;

$mineconomy = Mineconomy::getInstance();
```

**How to retrieve a players balance:**
```php
/* $player can be either a players name or the actual Player class **/

Mineconomy::getInstance()->getFunds($player);

or

Mineconomy::getInstance()->getFunds("Steve");
```

**How to add funds to a players balance:**
```php
/* $player can be either a players name or the actual Player class **/

Mineconomy::getInstance()->addFunds($player);

or

Mineconomy::getInstance()->addFunds("Steve");
```

**How to remove funds to a players balance:**
```php
/* $player can be either a players name or the actual Player class **/

Mineconomy::getInstance()->removeFunds($player);

or

Mineconomy::getInstance()->removeFunds("Steve");
```

**How to set funds to a players balance:**
```php
/* $player can be either a players name or the actual Player class **/

Mineconomy::getInstance()->setFunds($player);

or

Mineconomy::getInstance()->setFunds("Steve");
```

**How to check if a player has a balance before doing anything:**
```
/* $player can be either a players name or the actual Player class **/

if (Mineconomy::getInstance()->hasBalance($player)) {
    Mineconomy::getInstance()->addFunds($player, $amount);
    $player->sendMessage("Successfully added $" . number_format($amount) . " to " . $player->getName() . " balance!");
} else {
    $player->sendMessage($player->getName() . " does not have a balance!");
}

or

$targetName = "Steve";

if (Mineconomy::getInstance()->hasBalance($targetName)) {
    Mineconomy::getInstance()->addFunds($targetName, $amount);
    $player->sendMessage("Successfully added $" . number_format($amount) . " to " . $targetName . " balance!");
} else {
    $player->sendMessage($targetName . " does not have a balance!");
}
```

# ScoreHud
**Mineconomy** has ScoreHud support!

Link to addon:

[Click me!](https://github.com/Terpz710/MineconomyScoreTag)
