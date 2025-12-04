<?php
namespace Regur\LMVC\Framework\Database\Core;

class DB
{
    private static ?\PDO $pdo = null;

    /**
     * Constructor to initialize the PDO connection.
     *
     * @param array $config Configuration array with keys: host, database, username, password, charset.
     */
    public function __construct(array $config)
    {
        if (self::$pdo === null) {
            $this->instantiate(
                $config['host'],
                $config['database'],
                $config['username'],
                $config['password'],
                $config['charset'] ?? 'utf8mb4'
            );
        }
    }

    /**
     * Initializes the PDO connection.
     *
     * @param string $host     Database host.
     * @param string $dbname   Database name.
     * @param string $username Database username.
     * @param string $password Database password.
     * @param string $charset  Character set for the connection.
     */
    private function instantiate($host, $dbname, $username, $password, $charset)
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        try {
            self::$pdo = new \PDO(
                $dsn,
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $e) {
            throw new \PDOException("DB Connection failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Returns the PDO connection instance.
     *
     * @return \PDO The PDO connection instance.
     */
    public function getConnection(): \PDO
    {
        return self::$pdo;
    }
}
