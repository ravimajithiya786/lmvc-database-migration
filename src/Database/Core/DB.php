<?php
namespace Regur\LMVC\Framework\Database\Core;

class DB
{
    private static $connection = null;

    /**
     * Constructor to initialize database connection based on driver
     *
     * @param array $config Required keys:
     *   driver: mysql|mariadb|pgsql|postgres|postgresql
     *   host, database, username, password, charset, port
     */
    public function __construct(array $config)
    {
        if (self::$connection === null) {
            $driver = strtolower($config['driver'] ?? 'mysql');

            switch ($driver) {
                case 'mysql':
                case 'mariadb':
                    self::$connection = $this->connect_mysql($config);
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
    private function connect_mysql($config)
    {
        $host = $config['host'];
        $dbname = $config['database'];
        $charset = $config['charset'] ?? 'utf8mb4';
        $port = $config['port'] ?? 3306;

        // resolve docker hostname to IP
        $resolvedHost = gethostbyname($host);

        $dsn = "mysql:host={$resolvedHost};port={$port};dbname={$dbname};charset={$charset}";

        $pdo =  new \PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $pdo;
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

        // resolve docker hostname to IP
        $resolvedHost = gethostbyname($host);

        // Ensure client encoding
        $dsn = "pgsql:host={$resolvedHost};port={$port};dbname={$dbname};options='--client_encoding={$charset}'";

        $pdo = new \PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );

        return $pdo;
    }

    /* ============================================================
        GET CONNECTION
    ============================================================ */
    public function getConnection()
    {
        return self::$connection;
    }
}
