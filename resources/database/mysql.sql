-- #!mysql

-- #{ table
    -- #{ money
        CREATE TABLE IF NOT EXISTS money (
            uuid VARCHAR(36) PRIMARY KEY,
            name VARCHAR(16) NOT NULL,
            balance INT NOT NULL DEFAULT 0
        );
    -- #}
-- #}

-- #{ money
    -- #{ create
        -- # :uuid string
        -- # :name string
        -- # :balance int
        INSERT INTO money (uuid, name, balance)
        VALUES (:uuid, :name, :balance)
        ON DUPLICATE KEY UPDATE name = VALUES(name);
    -- #}

    -- #{ has
        -- # :uuid string
        SELECT balance FROM money WHERE uuid = :uuid;
    -- #}

    -- #{ get
        -- # :uuid string
        SELECT balance FROM money WHERE uuid = :uuid;
    -- #}

    -- #{ get_by_name
        -- # :name string
        SELECT uuid FROM money WHERE name = :name;
    -- #}

    -- #{ add
        -- # :uuid string
        -- # :amount int
        UPDATE money SET balance = balance + :amount WHERE uuid = :uuid;
    -- #}

    -- #{ remove
        -- # :uuid string
        -- # :amount int
        UPDATE money SET balance = balance - :amount WHERE uuid = :uuid;
    -- #}

    -- #{ set
        -- # :uuid string
        -- # :amount int
        UPDATE money SET balance = :amount WHERE uuid = :uuid;
    -- #}

    -- #{ top
        SELECT name, balance FROM money ORDER BY balance DESC LIMIT 10;
    -- #}
-- #}