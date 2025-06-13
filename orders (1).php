<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    echo "<script>
        alert('Please Logged In');
        window.location.href = 'login.php'; 
    </script>";
    exit;
}

$userID = $_SESSION['userID'];

$servername = "localhost";
$username = "u663344503_221024";
$password = "Database_3";
$dbname = "u663344503_users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$sql = "SELECT orderID, orderDate FROM orders WHERE userID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
$conn->close();

function formatDate($date) {
    return date("F j, Y", strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Aurum Jewelry PH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color:  #F26B8A;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            
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
        .orders-container {
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
        }
        .order-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
        }
        .close-modal {
            float: right;
            cursor: pointer;
            font-size: 1.5em;
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
    display: flex;
    flex-direction: column;
}

body > .navbar,
body > .orders-container {
   
    flex-shrink: 0;
}

.orders-container {
    flex-grow: 1; 
}

.footer {
    flex-shrink: 0;
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

<div class="orders-container">
    <h1>My Orders</h1>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-item" data-orderid="<?= htmlspecialchars($order['orderID']) ?>"
                 data-orderdate="<?= htmlspecialchars(formatDate($order['orderDate'])) ?>">
                <p><strong>Order ID:</strong> #<?= htmlspecialchars($order['orderID']) ?></p>
                <p><strong>Date:</strong> <?= formatDate($order['orderDate']) ?></p>
                <button class="view-status-button">View Delivery Status</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>
</div>

<div class="modal" id="deliveryStatusModal">
    <div class="modal-content">
        <span class="close-modal" id="closeModalBtn">&times;</span>
        <h2>Delivery Status</h2>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    const modal = document.getElementById('deliveryStatusModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalContent = document.getElementById('modalContent');

    document.querySelectorAll('.view-status-button').forEach(button => {
        button.addEventListener('click', function () {
            const parent = this.closest('.order-item');
            const orderID = parent.getAttribute('data-orderid');
            const orderDate = parent.getAttribute('data-orderdate');

            modalContent.innerHTML = `
                <p><strong>Order Number:</strong> #${orderID}</p>
                <p><strong>Order Date:</strong> ${orderDate}</p>
                <hr>
                <ul class="timeline">
                    <li>Packed</li>
                    <li>Shipped</li>
                    <li>On Delivery</li>
                    <li>Delivered</li>
                </ul>
            `;
            modal.style.display = "flex";
        });
    });

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = "none";
    });

    window.addEventListener('click', e => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
</script>


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
</body>

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
