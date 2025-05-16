<?php

class WordPress_DB_Connector {
    
    private static $connection = null;

    
    public static function getConnection() {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }
        return self::$connection;
    }

    
    private static function createConnection() {
        $config = self::getDatabaseConfig();
        
        $mysqli = new mysqli(
            $config['DB_HOST'], 
            $config['DB_USER'], 
            $config['DB_PASSWORD'], 
            $config['DB_NAME']
        );

        if ($mysqli->connect_errno) {
            throw new RuntimeException(
                "Failed to connect to MySQL: " . $mysqli->connect_error
            );
        }

        $mysqli->set_charset("utf8mb4");
        return $mysqli;
    }

    
    private static function getDatabaseConfig() {
        $config_path = dirname(__DIR__) . '/wp-config.php';
        
        if (!file_exists($config_path)) {
            throw new RuntimeException("wp-config.php not found");
        }

        $content = file_get_contents($config_path);
        $content = preg_replace('/\/\*.*?\*\/|\/\/.*?(\r\n?|\n)/s', '', $content);
        
        $defines = [];
        foreach (explode("\n", $content) as $line) {
            if (strpos(trim($line), 'define') === 0) {
                preg_match('/define\s*\(\s*[\'"](\w+)[\'"]\s*,\s*[\'"](.*?)[\'"]\s*\)/', $line, $matches);
                if (!empty($matches) && strpos($matches[1], 'DB_') === 0) {
                    $defines[$matches[1]] = $matches[2];
                }
            }
        }

        $required = ['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST'];
        foreach ($required as $key) {
            if (!isset($defines[$key])) {
                throw new RuntimeException("Missing required database configuration: $key");
            }
        }

        return $defines;
    }

    
    public static function closeConnection() {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}

/*
 * Usage Example:
 * 
 * try {
 *     $db = WordPress_DB_Connector::getConnection();
 *     $result = $db->query("SELECT * FROM wp_posts LIMIT 1");
 *     // Process results...
 * } catch (RuntimeException $e) {
 *     error_log($e->getMessage());
 *     // Handle error...
 * }
 */