<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in (session userID missing).']);
    exit;
}

$servername = "localhost";
$username = "u663344503_221024";
$password = "Database_3";
$dbname = "u663344503_users";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$userID = $_SESSION['userID'];

$sql = "SELECT p.productID, p.productName, p.price, COUNT(c.productID) AS quantity
        FROM cart c
        JOIN products p ON c.productID = p.productID
        WHERE c.userID = ?
        GROUP BY p.productID, p.productName, p.price";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed for cart query: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$totalAmount = 0;
$cartItems = [];

while ($row = $result->fetch_assoc()) {
    $totalAmount += $row['price'] * $row['quantity'];
    $cartItems[] = $row;
}

$stmt->close();

if ($totalAmount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty or invalid.']);
    $conn->close();
    exit;
}

$orderSql = "INSERT INTO orders (userID, orderDate, status) VALUES (?, NOW(), 'Pending')";
$stmtOrder = $conn->prepare($orderSql);

if (!$stmtOrder) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed for order insert: ' . $conn->error]);
    exit;
}

$stmtOrder->bind_param("i", $userID);  

if (!$stmtOrder->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to create order: ' . $stmtOrder->error]);
    $stmtOrder->close();
    $conn->close();
    exit;
}

$orderID = $stmtOrder->insert_id;
$stmtOrder->close();

foreach ($cartItems as $item) {
    $stmtItem = $conn->prepare("INSERT INTO order_items (orderID, productID, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$stmtItem) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed for order_items insert: ' . $conn->error]);
        $conn->close();
        exit;
    }
    $stmtItem->bind_param("iiid", $orderID, $item['productID'], $item['quantity'], $item['price']);
    if (!$stmtItem->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to insert order item: ' . $stmtItem->error]);
        $stmtItem->close();
        $conn->close();
        exit;
    }
    $stmtItem->close();
}

$clearCartSql = "DELETE FROM cart WHERE userID = ?";
$stmtClear = $conn->prepare($clearCartSql);
if (!$stmtClear) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed for clearing cart: ' . $conn->error]);
    $conn->close();
    exit;
}
$stmtClear->bind_param("i", $userID);
if (!$stmtClear->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to clear cart: ' . $stmtClear->error]);
    $stmtClear->close();
    $conn->close();
    exit;
}
$stmtClear->close();

$conn->close();

echo json_encode(['success' => true, 'message' => 'Order created successfully.']);
?>
