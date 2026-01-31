<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect('dashboard.php');
}

$database = new Database();
$conn = $database->getConnection();

// Prevent deleting admin users
$stmt = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    redirect('dashboard.php');
}

if ($user['role'] === 'admin') {
    // Do not allow deleting admin accounts
    redirect('dashboard.php');
}

// Safe to delete
$del = $conn->prepare("DELETE FROM users WHERE id = ?");
$del->execute([$id]);

redirect('dashboard.php');

?>