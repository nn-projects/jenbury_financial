<?php
// This script will create the database and import the sample data

// Database configuration
$host = 'localhost';
$username = 'root'; // Using root to create the database and user
$password = ''; // Root password (empty for default)
$database = 'jenbury_financial';
$newUser = 'Jenbury';
$newPassword = 'jenbury';

echo "Starting database setup...\n";

// Connect to MySQL as root
try {
    echo "Connecting to MySQL as root...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL successfully as root.\n";
    
    // Create the database if it doesn't exist
    echo "Creating database '$database'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$database' created or already exists.\n";
    
    // Create the user if it doesn't exist and grant privileges
    echo "Creating user '$newUser'...\n";
    $pdo->exec("CREATE USER IF NOT EXISTS '$newUser'@'localhost' IDENTIFIED BY '$newPassword'");
    echo "Granting privileges to user '$newUser'...\n";
    $pdo->exec("GRANT ALL PRIVILEGES ON `$database`.* TO '$newUser'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "User '$newUser' created and granted privileges.\n";
    
    // Connect to the database
    echo "Connecting to database '$database'...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database '$database'.\n";
    
    // Import the sample data from the SQL file
    $sqlFile = __DIR__ . '/config/sample_data.sql';
    echo "Checking for sample data file: $sqlFile\n";
    if (file_exists($sqlFile)) {
        echo "Sample data file found. Importing...\n";
        $sql = file_get_contents($sqlFile);
        
        // Split the SQL file into individual statements
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    echo "Error executing statement: " . $e->getMessage() . "\n";
                    echo "Statement: " . $statement . "\n";
                }
            }
        }
        echo "Sample data imported successfully.\n";
    } else {
        echo "Sample data file not found: $sqlFile\n";
    }
    
    echo "Database setup completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}