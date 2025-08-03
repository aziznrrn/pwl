<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getDbConnection(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dbHost = config('DB_HOST');
        $dbName = config('DB_NAME');
        $dbUser = config('DB_USER');
        $dbPass = config('DB_PASS');

        try {
            $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            if ($e->getCode() === 1049) { 
                $pdo = new PDO("mysql:host={$dbHost}", $dbUser, $dbPass);
                $pdo->exec("CREATE DATABASE `{$dbName}`");
                $pdo->exec("USE `{$dbName}`");
            } else {
                throw $e;
            }
        }
    }
    return $pdo;
}

function initializeDatabase(): void
{
    $pdo = getDbConnection();

    $tables = ['departments', 'lecturers', 'staff'];
    $tableExists = $pdo->query("SHOW TABLES LIKE 'departments'")->rowCount() > 0;

    if (!$tableExists) {
        $sql = <<<'SQL'
        CREATE TABLE departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        );

        CREATE TABLE lecturers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            nidn VARCHAR(20) NOT NULL UNIQUE,
            department_id INT,
            FOREIGN KEY (department_id) REFERENCES departments(id)
        );

        CREATE TABLE staff (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            nip VARCHAR(20) NOT NULL UNIQUE,
            work_unit VARCHAR(100)
        );
        SQL;
        $pdo->exec($sql);

        
        $departments = [
            [1, 'Teknik Elektro'],
            [2, 'Teknik Mesin'],
            [3, 'Teknik Sipil'],
            [4, 'Informatika']
        ];

        $lecturers = [
            [1, 'Dr. Budi Santoso', '0012345678', 1],
            [2, 'Ir. Ani Wijaya, M.T.', '0023456789', 2],
            [3, 'Prof. Dr. Ir. Joko Susilo', '0034567890', 3],
            [4, 'Citra Lestari, S.Kom., M.Kom.', '0045678901', 4]
        ];

        $staff = [
            [1, 'Agus Setiawan', '198501012010011001', 'Administrasi Akademik'],
            [2, 'Dewi Puspita', '199002022015022002', 'Keuangan'],
            [3, 'Eko Prasetyo', '198803032014031003', 'Urusan Umum']
        ];

        $stmt = $pdo->prepare("INSERT INTO departments (id, name) VALUES (?, ?)");
        foreach ($departments as $dept) {
            $stmt->execute($dept);
        }

        $stmt = $pdo->prepare("INSERT INTO lecturers (id, name, nidn, department_id) VALUES (?, ?, ?, ?)");
        foreach ($lecturers as $lecturer) {
            $stmt->execute($lecturer);
        }

        $stmt = $pdo->prepare("INSERT INTO staff (id, name, nip, work_unit) VALUES (?, ?, ?, ?)");
        foreach ($staff as $s) {
            $stmt->execute($s);
        }
    }
}

initializeDatabase();
