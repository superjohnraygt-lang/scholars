<?php
require_once 'auth_check.php';

// Only admin can delete
if ($_SESSION['role'] != 'admin') {
    header('Location: read.php');
    exit();
}

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM scholarship_applications WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header('Location: read.php');
exit();
?>