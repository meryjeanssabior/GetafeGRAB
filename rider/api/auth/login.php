<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? ''; // Email or Phone
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        echo json_encode([
            'success' => true,
            'role' => $user['role']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid email/phone or password'
        ]);
    }
}
?>
