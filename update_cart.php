<?php
session_start();
$servername = "localhost";
$username = "u663344503_221024";
$password = "Database_3";
$dbname = "u663344503_users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    header("Location: login.php");
    exit();
}

$action = $_POST['action'] ?? '';
$productID = $_POST['productID'] ?? 0;

if ($action && $productID) {
    if ($action === 'increase') {
        $sql = "INSERT INTO cart (userID, productID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $productID);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'decrease') {
        $sql = "DELETE FROM cart WHERE userID = ? AND productID = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $productID);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM cart WHERE userID = ? AND productID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userID, $productID);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header("Location: cart.php");
exit();
?>
