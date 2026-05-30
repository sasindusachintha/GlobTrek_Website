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
<<<<<<< HEAD
        'hotels' => [
            'contact_person_name' => 'VARCHAR(100) DEFAULT NULL',
            'contact_phone'       => 'VARCHAR(50) DEFAULT NULL',
            'contact_email'       => 'VARCHAR(100) DEFAULT NULL',
        ],
        'transport' => [
            'contact_name'  => 'VARCHAR(100) DEFAULT NULL',
            'contact_phone' => 'VARCHAR(50) DEFAULT NULL',
        ],
=======
>>>>>>> 4879ef52bfb37ce94e2529f2b9dedf97f8eeefca
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

<<<<<<< HEAD
    if (configTableExists('hotels') && columnExists('hotels', 'contact_person_name') && columnExists('hotels', 'contact_phone')) {
        $result = $conn->query('SELECT COUNT(*) AS total FROM hotels');
        $hotelCount = $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;

        if ($hotelCount === 0) {
            $conn->query("
                INSERT INTO hotels (name, location, contact_person_name, contact_phone, contact_email, status) VALUES
                ('Ocean Pearl Hotel', 'Galle', 'Nimal Perera', '+94 77 123 4567', NULL, 'pending'),
                ('Hillview Grand', 'Kandy', 'Asha Fernando', '+94 76 234 5678', NULL, 'confirmed'),
                ('City Lights Inn', 'Colombo', 'Ruwan Silva', '+94 71 345 6789', NULL, 'pending')
            ");
        } else {
            $conn->query("
                UPDATE hotels
                SET contact_person_name = CASE MOD(hotel_id, 3)
                    WHEN 1 THEN 'Nimal Perera'
                    WHEN 2 THEN 'Asha Fernando'
                    ELSE 'Ruwan Silva'
                END
                WHERE contact_person_name IS NULL OR contact_person_name = ''
            ");
            $conn->query("
                UPDATE hotels
                SET contact_phone = CASE MOD(hotel_id, 3)
                    WHEN 1 THEN '+94 77 123 4567'
                    WHEN 2 THEN '+94 76 234 5678'
                    ELSE '+94 71 345 6789'
                END
                WHERE contact_phone IS NULL OR contact_phone = ''
            ");
        }
    }

    if (configTableExists('transport') && columnExists('transport', 'contact_name') && columnExists('transport', 'contact_phone')) {
        $result = $conn->query('SELECT COUNT(*) AS total FROM transport');
        $transportCount = $result ? intval($result->fetch_assoc()['total'] ?? 0) : 0;

        if ($transportCount === 0) {
            $conn->query("
                INSERT INTO transport (type, contact_name, contact_phone, status) VALUES
                ('Airport Shuttle', 'Kasun Jayasinghe', '+94 75 456 7890', 'ready'),
                ('Private Van', 'Malith Gunasekara', '+94 72 567 8901', 'scheduled'),
                ('Tour Coach', 'Dinuka Samarasinghe', '+94 70 678 9012', 'confirmed')
            ");
        } else {
            $conn->query("
                UPDATE transport
                SET contact_name = CASE MOD(transport_id, 3)
                    WHEN 1 THEN 'Kasun Jayasinghe'
                    WHEN 2 THEN 'Malith Gunasekara'
                    ELSE 'Dinuka Samarasinghe'
                END
                WHERE contact_name IS NULL OR contact_name = ''
            ");
            $conn->query("
                UPDATE transport
                SET contact_phone = CASE MOD(transport_id, 3)
                    WHEN 1 THEN '+94 75 456 7890'
                    WHEN 2 THEN '+94 72 567 8901'
                    ELSE '+94 70 678 9012'
                END
                WHERE contact_phone IS NULL OR contact_phone = ''
            ");
        }
    }

=======
>>>>>>> 4879ef52bfb37ce94e2529f2b9dedf97f8eeefca
}


// RUN SCHEMA CHECK
ensureSchema();


