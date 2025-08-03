<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDbConnection();

if ($method === 'GET') {
    if (isset($_GET['table'])) {
        $table = $_GET['table'];
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            if ($table === 'lecturers') {
                $stmt = $pdo->query('SELECT l.id, l.name, l.nidn, d.name AS department_name FROM lecturers l JOIN departments d ON l.department_id = d.id');
            } else {
                $stmt = $pdo->query("SELECT * FROM {$table}");
            }
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $table = $data['table'];
    unset($data['table']);

    if (isset($data['id']) && !empty($data['id'])) {
        $id = $data['id'];
        unset($data['id']);
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "{$key} = :{$key}";
        }
        $updateString = implode(', ', $updates);
        $sql = "UPDATE {$table} SET {$updateString} WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $data['id'] = $id;
        $stmt->execute($data);
    } else {
        unset($data['id']);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    echo json_encode(['status' => 'success']);
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $table = $data['table'];
    $id = $data['id'];

    $sql = "DELETE FROM {$table} WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    echo json_encode(['status' => 'success']);
}
