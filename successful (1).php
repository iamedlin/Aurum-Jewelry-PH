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

$userID = $_SESSION['userID'] ?? 1; 

$conn->query("INSERT INTO orders (userID) VALUES ($userID)");
$orderID = $conn->insert_id;

$userInfo = [];
$userStmt = $conn->prepare("SELECT name, contact, address FROM user_info WHERE userID = ?");
if ($userStmt) {
    $userStmt->bind_param("i", $userID);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userResult->num_rows > 0) {
        $userInfo = $userResult->fetch_assoc();
    } else {
        error_log("No user found in user_info for userID = $userID");
    }
    $userStmt->close();
} else {
    die("User info query failed: " . $conn->error);
}

$items = [];
$cartStmt = $conn->prepare("
    SELECT p.productName, p.price
    FROM cart c
    JOIN products p ON c.productID = p.productID
    WHERE c.userID = ?
");
if ($cartStmt) {
    $cartStmt->bind_param("i", $userID);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    while ($row = $cartResult->fetch_assoc()) {
        $items[] = $row;
    }
    $cartStmt->close();
} else {
    die("Cart fetch failed: " . $conn->error);
}

$clearCartStmt = $conn->prepare("DELETE FROM cart WHERE userID = ?");
if ($clearCartStmt) {
    $clearCartStmt->bind_param("i", $userID);
    $clearCartStmt->execute();
    $clearCartStmt->close();
} else {
    die("Cart delete failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful - Husi Apparel</title>
    <style>
        body { background-color:  #FDA4BA; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .success-container { max-width: 800px; background: #fff; padding: 20px; border-radius: 10px; text-align: center; }
        .order-details { text-align: left; margin-top: 20px; }
        .items-list .item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F26B8A; }
        .back-button { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #F26B8A; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="success-container">
    <h1>Order Successful!</h1>
    <p>Thank you for your purchase. Your order has been placed.</p>

    <div class="order-details">
        <h3>Order Summary</h3>
        <div class="items-list">
            <?php foreach ($items as $item): ?>
                <div class="item">
                    <div><?= htmlspecialchars($item['productName']) ?></div>
                    <div>Php <?= number_format($item['price'], 2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="shipping-info" style="margin-top: 20px;">
            <h3>Shipping Information</h3>
            <?php if (!empty($userInfo)): ?>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($userInfo['name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($userInfo['address']) ?></p>
                <p><strong>Contact Number:</strong> <?= htmlspecialchars($userInfo['contact']) ?></p>
            <?php else: ?>
                <p style="color:red;">User information not found. Check if userID <?= $userID ?> exists in the user_info table.</p>
            <?php endif; ?>
        </div>

        <div class="payment-info" style="margin-top: 20px;">
            <h3>Payment Method</h3>
            <p>Cash on Delivery</p>
        </div>
    </div>

    <a href="index.php" class="back-button">Back to Home</a>
</div>
</body>
</html>
<?php $conn->close(); ?>
