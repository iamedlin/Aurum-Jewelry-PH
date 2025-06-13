<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$userID = $_SESSION['userID'];

$servername = "localhost";
$username = "u663344503_221024";
$password = "Database_3";
$dbname = "u663344503_users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("SELECT profile FROM user_info WHERE userID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $userID);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$stmt->bind_result($profilePath);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("DELETE oi FROM order_items oi JOIN orders o ON oi.orderID = o.orderID WHERE o.userID = ?");
if (!$stmt) {
    die("Prepare failed for order_items delete: " . $conn->error);
}
$stmt->bind_param("i", $userID);
if (!$stmt->execute()) {
    die("Execute failed for order_items delete: " . $stmt->error);
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM orders WHERE userID = ?");
if (!$stmt) {
    die("Prepare failed for orders delete: " . $conn->error);
}
$stmt->bind_param("i", $userID);
if (!$stmt->execute()) {
    die("Execute failed for orders delete: " . $stmt->error);
}
$stmt->close();

if (!empty($profilePath)) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $profilePath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

$stmt = $conn->prepare("DELETE FROM user_info WHERE userID = ?");
if (!$stmt) {
    die("Prepare failed for user_info delete: " . $conn->error);
}
$stmt->bind_param("i", $userID);
if (!$stmt->execute()) {
    die("Execute failed for user_info delete: " . $stmt->error);
}
$stmt->close();

$conn->close();

session_unset();
session_destroy();

header("Location: index.php");
exit();
?>
