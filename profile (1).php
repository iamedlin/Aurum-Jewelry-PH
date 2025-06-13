<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID  = $_SESSION['userID'] ?? null;
$name    = $_SESSION['name'] ?? '';
$email   = $_SESSION['email'] ?? '';
$contact = $_SESSION['contact'] ?? '';
$address = $_SESSION['address'] ?? '';

$defaultProfile = 'uploads/default.jpg';

$profile = $_SESSION['profile'] ?? $defaultProfile;

$profileFilePath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($profile, '/');

if (!file_exists($profileFilePath) || empty($profile)) {
    $profile = $defaultProfile;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile - Aurum Jewelry Ph</title>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
    <style>
        header {
    background-color: #F26B8A;
    padding: 20px;
    color: white;
    text-align: center;
}

.header-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 40px;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            font-weight: bold;
            border: none;
            border-radius: 5px;
        }
        .delete {
            background-color: red;
            color: white;
        }
        .logout {
            background-color: black;
            color: white;
        }
        img.profile-pic {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #ccc;
        }
        .info-field {
            margin: 8px 0;
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

.footer-right i {
    font-size: 24px;
    color: #000; 
    margin: 0 8px;
}

.footer-right a:hover i {
    color: #F26B8A; 
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
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content-wrapper {
    flex: 1; 
    padding-top: 40px; 
    text-align: center;
}

footer.footer {
    margin-top: auto; 
}

.button-group {
    display: flex;
    justify-content: center; 
    gap: 20px; 
    margin: 20px 0;
}

.button-group form {
    margin: 0;
}


    </style>
    
</head>

<body>
 <header>
  <div class="header-container">
    <div class="logo">Aurum Jewelry Ph</div>
  </div>
</header>
<body>
  <div class="content-wrapper">

    <img src="aurum.jpg" alt="Aurum Jewelry PH Logo" style="width:300px; margin-bottom: 20px;" />
    
    <h1> Profile </h1>

    <p class="info-field"><strong>Full Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <?php if (!empty($email)): ?>
        <p class="info-field"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <?php endif; ?>
    <?php if (!empty($contact)): ?>
        <p class="info-field"><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></p>
    <?php endif; ?>
    <?php if (!empty($address)): ?>
        <p class="info-field"><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
    <?php endif; ?>

    <div class="button-group">
    <form action="delete_account.php" method="post" onsubmit="return confirm('Are you sure you want to DELETE your account? This action cannot be undone.')">
        <button class="delete" type="submit">DELETE</button>
    </form>

    <form action="logout.php" method="post" onsubmit="return confirm('Are you sure you want to LOGOUT?')">
        <button class="logout" type="submit">LOGOUT</button>
    </form>

</div>

    <form action="index.php" method="get">
        <button type="submit" style="background-color: #FDA4BA; color: white;">BACK TO HOME</button>
    </form>

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
        <a href="https://www.tiktok.com/@aurumjewelryph" target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-tiktok"></i>
</a>

        <a href="https://www.facebook.com/share/1L5XYe1uUA/" target="_blank" rel="noopener noreferrer">
  <i class="fa-brands fa-facebook-f"></i>
</a>
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

