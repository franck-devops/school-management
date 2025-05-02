<?php
require_once 'config.php';
require_once 'db.php';
require_once 'auth.php';

header('Content-Type: application/json');

// Only allow superuser to perform backups
if (!isLoggedIn() || $_SESSION['user_role'] !== 'superuser') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Backup directory
$backupDir = '../backups/';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Create filename with timestamp
$backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';

try {
    // Get all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    if (empty($tables)) {
        throw new Exception('No tables found in database');
    }
    
    // Open backup file
    $handle = fopen($backupFile, 'w+');
    if (!$handle) {
        throw new Exception('Could not create backup file');
    }
    
    // Write SQL header
    fwrite($handle, "-- School Management System Database Backup\n");
    fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- Database: " . $db_name . "\n\n");
    
    // Loop through tables
    foreach ($tables as $table) {
        // Drop table if exists
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
        
        // Get create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        fwrite($handle, $row[1] . ";\n\n");
        
        // Get table data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keys = array_keys($row);
            $values = array_values($row);
            
            // Escape values
            $values = array_map(function($value) use ($pdo) {
                return $value === null ? 'NULL' : $pdo->quote($value);
            }, $values);
            
            fwrite($handle, "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n");
        }
        
        fwrite($handle, "\n");
    }
    
    fclose($handle);
    
    // Update last backup setting
    setSetting('last_backup', date('Y-m-d H:i:s'));
    
    echo json_encode([
        'success' => true,
        'message' => 'Backup created successfully',
        'file' => $backupFile
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Backup failed: ' . $e->getMessage()
    ]);
}
?>