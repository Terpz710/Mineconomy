-- #!sqlite

-- #{ table
    -- #{ money
        CREATE TABLE IF NOT EXISTS money (
            uuid TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            balance INTEGER NOT NULL DEFAULT 0
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
        ON CONFLICT(uuid) DO UPDATE SET name = excluded.name;
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