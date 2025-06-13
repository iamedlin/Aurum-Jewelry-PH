<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$productID = $data['productID'] ?? null;

if (!$productID) {
    echo json_encode(['success' => false, 'error' => 'Missing productID']);
    exit;
}

$servername = "localhost";
$username = "u663344503_221024";
$password = "Database_3";
$dbname = "u663344503_users";

$conn = new mysqli($servername, $username, $password, $dbname);
$dateAdded = date('Y-m-d H:i:s');
$userID = $_SESSION['userID'];

$stmt = $conn->prepare("INSERT INTO cart (userID, productID, dateAdded) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $userID, $productID, $dateAdded);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
