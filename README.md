Description:
This PHP class provides a secure way to connect to a WordPress database by automatically extracting credentials from the wp-config.php file. It implements:

Automatic config parsing - Reads DB_NAME, DB_USER, DB_PASSWORD, and DB_HOST from wp-config.php

Singleton pattern - Maintains a single database connection

Secure connection - Uses MySQLi with UTF8MB4 charset

Error handling - Throws exceptions instead of echoing errors

Connection management - Includes method to close connection
