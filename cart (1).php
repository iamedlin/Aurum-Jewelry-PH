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
    echo "<script>
        alert('Please Log In');
        window.location.href = 'login.php'; 
    </script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_product'])) {
    $deleteProductID = intval($_POST['delete_product']);
    $deleteSql = "DELETE FROM cart WHERE userID = ? AND productID = ?";
    $stmtDelete = $conn->prepare($deleteSql);
    $stmtDelete->bind_param("ii", $userID, $deleteProductID);
    $stmtDelete->execute();
    $stmtDelete->close();
}

$sql = "SELECT p.productID, p.productName, p.price, COUNT(c.productID) AS quantity
        FROM cart c
        JOIN products p ON c.productID = p.productID
        WHERE c.userID = ?
        GROUP BY p.productID, p.productName, p.price";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $orderSql = "INSERT INTO orders (userID, orderDate, status, totalAmount) VALUES (?, NOW(), 'Pending', ?)";
    $stmtOrder = $conn->prepare($orderSql);
    $stmtOrder->bind_param("id", $userID, $total);
    $stmtOrder->execute();
    $stmtOrder->close();

    $clearCartSql = "DELETE FROM cart WHERE userID = ?";
    $stmtClearCart = $conn->prepare($clearCartSql);
    $stmtClearCart->bind_param("i", $userID);
    $stmtClearCart->execute();
    $stmtClearCart->close();

    header("Location: successful.php");
    exit();
}

$userSql = "SELECT name, contact, address FROM user_info WHERE userID = ?";
$stmtUser = $conn->prepare($userSql);
$stmtUser->bind_param("i", $userID);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userInfo = $userResult->fetch_assoc();
$stmtUser->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Aurum Jewelry PH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            background-color: white;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            background-color: #F26B8A;
            width: 100%;
            padding: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo { font-size: 1.5em; font-weight: bold; margin-left: 80px; }
        .navbar .nav-links {
            display: flex;
            gap: 20px;
            margin-right: 150px;
        }
        .navbar .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
        .cart-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            gap: 20px;
            margin-top: 100px;
        }
        .cart-items, .checkout-info {
            background-color: #FDA4BA;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .cart-items { flex: 2; }
        .cart-items h2, .checkout-info h2 { font-size: 1.8em; margin-bottom: 20px; }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .item-details h4 { font-size: 1.2em; margin-bottom: 5px; }
        .item-price { font-weight: bold; color: #333; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; }
        .quantity-controls button { padding: 5px 10px; font-size: 1em; }
        .checkout-info { flex: 1; }
        .checkout-section { margin-bottom: 20px; }
        .payment-method { display: flex; gap: 10px; }
        .checkout-button {
            width: 100%;
            padding: 15px;
            font-size: 1.2em;
            font-weight: bold;
            color: #fff;
            background-color: #ff8da1;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }
        
         .footer {
    background-color: #F26B8A;
    color: #333;
    display: flex;
    justify-content: center;
    gap: 100px; 
    align-items: center;
    padding: 20px;
    text-align: center;
    flex-wrap: wrap;
}

.footer-left, .footer-center, .footer-right {
    min-width: 200px;
}

.footer a {
    color: #000;
    text-decoration: none;
}

.footer a:hover {
    text-decoration: underline;
}

.footer-right i {
    font-size: 1.5em;
    margin: 0 5px;
}

.footer-center p, .footer-right p {
    margin-bottom: 8px;
}


.modal {
    display: none;
    position: fixed;
    z-index: 99;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 12px;
    width: 80%;
    max-width: 600px;
    text-align: center;
    position: relative;
}

.modal .close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 1.5em;
    font-weight: bold;
    cursor: pointer;
}

.policy-text {
    text-align: left;
    line-height: 1.6;
}
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.cart-container {
    flex: 1; 
}

.footer {
    background-color: #F26B8A;
    color: #333;
    width: 100%; 
    display: flex;
    justify-content: center;
    gap: 100px;
    align-items: center;
    padding: 20px;
    text-align: center;
    flex-wrap: wrap;
    margin-top: auto;
}

        .cart-item form {
            display: inline;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .delete-btn:hover {
            background-color: #e60000;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Aurum Jewelry PH</div>
    <div class="nav-links">
        <a href="index.php">HOME</a>
        <a href="collections.php">COLLECTIONS</a>
        <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        <a href="orders.php"><i class="fas fa-box"></i></a>
        <a href="profile.php"><i class="fas fa-user"></i></a>
    </div>
</div>

<div class="cart-container">

    <div class="cart-items">
        <h2>Your Cart</h2>

        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <div class="cart-item">
                    <div class="item-details">
                        <h4><?= htmlspecialchars($item['productName']) ?></h4>
                        <p class="item-price">Php <?= number_format($item['price'], 2) ?></p>
                        <div class="quantity-controls">
    <form method="POST" action="update_cart.php" style="display: inline;">
        <input type="hidden" name="action" value="decrease">
        <input type="hidden" name="productID" value="<?= $item['productID'] ?>">
        <button type="submit">-</button>
    </form>

    <span><?= $item['quantity'] ?></span>

    <form method="POST" action="update_cart.php" style="display: inline;">
        <input type="hidden" name="action" value="increase">
        <input type="hidden" name="productID" value="<?= $item['productID'] ?>">
        <button type="submit">+</button>
    </form>

    <form method="POST" action="update_cart.php" style="display: inline;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="productID" value="<?= $item['productID'] ?>">
        <button type="submit" style="background-color: red; color: white;">Delete</button>
    </form>
</div>

                        
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <div class="checkout-info">
        <h2>Shipping & Payment</h2>
        <div class="checkout-section">
            <h3>Shipping Information</h3>
            <?php if ($userInfo): ?>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($userInfo['name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($userInfo['address']) ?></p>
                <p><strong>Contact Number:</strong> <?= htmlspecialchars($userInfo['contact']) ?></p>
            <?php else: ?>
                <p><strong>Shipping info not found.</strong></p>
            <?php endif; ?>
        </div>

        <div class="checkout-section">
            <h3>Payment Method</h3>
            <div class="payment-method">
                <label>
                    <input type="radio" name="payment" value="cod" checked>
                    Cash on Delivery
                </label>
            </div>
        </div>

        <form method="POST" id="checkoutForm">
            <button type="submit" name="checkout" class="checkout-button">Proceed to Checkout</button>
        </form>
    </div>
</div>


<script>
function proceedToCheckout() {
    const loggedIn = <?php echo json_encode(isset($_SESSION['userID'])); ?>;
    if (!loggedIn) {
        alert("Please log in to proceed to checkout.");
        window.location.href = "login.php";
        return;
    }

    fetch('create_order.php', {
        method: 'POST',
        body: JSON.stringify({}),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = "orders.php";
        } else {
            alert("Error creating order: " + data.error);
        }
    })
    .catch(() => alert("Unexpected error occurred."));
}
</script>

</div>

</body>


<footer class="footer">
    <div class="footer-left">
        <a href="#" id="storePolicyLink">Store Policy</a><br>
        <a href="#" id="paymentLink">Mode of Payment</a>
    </div>
    <div class="footer-center">
    <p>Email:<a href="https://mail.google.com/mail/u/3/?ogbl#inbox" target="_blank" rel="noopener noreferrer">aurumjewelryph@gmail.com</a></p>
</div>
    <div class="footer-right">
        <p>Follow us:</p>
        <a href="https://www.tiktok.com/@aurumjewelryph?_t=ZS-8wZVm7We6R5&_r=1" target="_blank">
            <i class="fab fa-tiktok"></i>
        </a>
        <a href="https://www.facebook.com/share/1L5XYe1uUA/" target="_blank">
            <i class="fab fa-facebook"></i>
        </a>
    </div>
</footer>

<div id="storePolicyModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="policy-text">
            <p><strong>✔ Guaranteed non-tarnish and hypoallergenic</strong></p>
            <p>✔ Minimalist and dainty designs</p>
            <p><strong>REMINDERS:</strong></p>
            <ul>
                <li>All items are brand new and on-hand (ready to ship)</li>
                <li>Please finalize your order before checking out</li>
                <li>Actual photos posted (what you see is what you get)</li>
                <li>For questions/clarifications, please message us for details and we'll gladly answer it</li>
                <li>We will be sending photos of the product(s) before ship-out for quality check</li>
                <li>We accept payments thru Cash on Delivery (COD)</li>
                <li>Please don't forget to give us a 5-star rating!</li>
            </ul>
            <p>Thank you and happy shopping!</p>
            <p>#aurumjewelryph</p>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById("storePolicyModal");
    const link = document.getElementById("storePolicyLink");
    const closeBtn = document.querySelector(".modal .close");

    link.onclick = function(e) {
        e.preventDefault();
        modal.style.display = "block";
    }

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close close-payment">&times;</span>
        <div class="policy-text">
            <p><strong>MODE OF PAYMENT</strong></p>
            <ul>
                <li>Cash on Delivery</li>
            </ul>
        </div>
    </div>
</div>

<script>
    const paymentModal = document.getElementById("paymentModal");
    const paymentLink = document.getElementById("paymentLink");
    const closePayment = document.querySelector(".close-payment");

    paymentLink.onclick = function(e) {
        e.preventDefault();
        paymentModal.style.display = "block";
    }

    closePayment.onclick = function() {
        paymentModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
        if (event.target === paymentModal) {
            paymentModal.style.display = "none";
        }
    }
</script>

</html>
