<?php
namespace Regur\LMVC\Framework\Database\Core;

class DB
{
    private static $connection = null;

    /**
     * Constructor to initialize database connection based on driver
     *
     * @param array $config Required keys:
     *   driver: mysql|pgsql|mariadb
     *   host, database, username, password, charset, port (optional)
     */
    public function __construct(array $config)
    {
        if (self::$connection === null) {
            $driver = strtolower($config['driver'] ?? 'mysql');

            switch ($driver) {
                case 'mysql':
                case 'mariadb':
                    self::$connection = $this->connect_mysql_family($config);
                    break;

                case 'pgsql':
                case 'postgres':
                case 'postgresql':
                    self::$connection = $this->connect_pgsql($config);
                    break;

                default:
                    throw new \Exception("Unsupported PDO driver: " . $driver);
            }
        }
    }

    /* ============================================================
        MYSQL / MARIADB (PDO)
    ============================================================ */
    private function connect_mysql_family($config)
    {
        $host = $config['host'];
        $dbname = $config['database'];
        $charset = $config['charset'] ?? 'utf8mb4';
        $port = $config['port'] ?? 3306;

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        return new \PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    /* ============================================================
        POSTGRESQL (PDO)
    ============================================================ */
    private function connect_pgsql($config)
    {
        $host = $config['host'];
        $dbname = $config['database'];
        $charset = $config['charset'] ?? 'utf8';
        $port = $config['port'] ?? 5432;

        // Ensure client encoding
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};options='--client_encoding={$charset}'";

        return new \PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    /* ============================================================
        GET CONNECTION
    ============================================================ */
    public function getConnection()
    {
        return self::$connection;
    }
}
