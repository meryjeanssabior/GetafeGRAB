<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'rider';

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $password, $role]);
        $userId = $pdo->lastInsertId();

        if ($role === 'driver') {
            $model = $_POST['model'] ?? '';
            $plate = $_POST['plate_number'] ?? '';
            $type = $_POST['type'] ?? 'car';

            $stmt = $pdo->prepare("INSERT INTO vehicles (driver_id, model, plate_number, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $model, $plate, $type]);
        }

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
