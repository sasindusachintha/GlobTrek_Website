<?php

// ERROR REPORTING (DEV ONLY)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// SESSION
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}


// DATABASE CONFIG
$host = "localhost";
$username = "root";
$password = "";
$database = "globtrek_db";


// DATABASE CONNECTION (SINGLE SOURCE)
function db() {
    static $conn;

    if ($conn) {
        return $conn;
    }

    global $host, $username, $password, $database;

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

    return $conn;
}


// CHECK COLUMN EXISTS
function columnExists($table, $column) {
    $conn = db();

    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = ? 
        AND COLUMN_NAME = ?
    ");

    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
}

function configTableExists($table) {
    $conn = db();

    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = ?
    ");

    $stmt->bind_param("s", $table);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0;
}


// AUTO SCHEMA UPDATE
function ensureSchema() {
    $conn = db();

    $schema = [
        'packages' => [
            'duration'   => 'VARCHAR(100) DEFAULT NULL',
            'transport'  => 'VARCHAR(100) DEFAULT NULL',
            'rating'     => 'VARCHAR(20) DEFAULT NULL',
            'highlights' => 'TEXT DEFAULT NULL',
            'itinerary'  => 'TEXT DEFAULT NULL',
            'included'   => 'TEXT DEFAULT NULL',
        ],
        'bookings' => [
            'return_date' => 'DATE DEFAULT NULL',
            'phone'       => 'VARCHAR(50) DEFAULT NULL',
        ],
        'queries' => [
            'email' => 'VARCHAR(100) DEFAULT NULL',
        ],
        'payments' => [
            'user_id' => 'INT(11) DEFAULT NULL',
        ],
    ];

    if (configTableExists('tour_packages')) {
        $schema['tour_packages'] = $schema['packages'];
    }

    foreach ($schema as $table => $columns) {
        if (!configTableExists($table)) {
            continue;
        }

        foreach ($columns as $column => $definition) {
            if (!columnExists($table, $column)) {
                $sql = sprintf(
                    "ALTER TABLE `%s` ADD COLUMN `%s` %s",
                    $conn->real_escape_string($table),
                    $conn->real_escape_string($column),
                    $definition
                );
                $conn->query($sql);
            }
        }
    }

    if (configTableExists('payments') && configTableExists('bookings') && columnExists('payments', 'user_id')) {
        $conn->query('
            UPDATE payments p
            LEFT JOIN bookings b ON p.booking_id = b.booking_id
            SET p.user_id = b.user_id
            WHERE p.user_id IS NULL
            AND b.user_id IS NOT NULL
        ');
    }

}


// RUN SCHEMA CHECK
ensureSchema();


