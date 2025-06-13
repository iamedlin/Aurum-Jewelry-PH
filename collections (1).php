<?php session_start();

session_start();
$loggedIn = isset($_SESSION['userID']); 
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aurum Jewelry PH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f5f5f5; color: #333; }
        .container { max-width: 2200px; margin: auto; padding: 0px; }
        .navbar {
            background-color: #F26B8A;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo { font-size: 1.5em; font-weight: bold; margin-left: 20px; }
        .navbar .nav-links {
            display: flex;
            gap: 20px;
            margin-right: 20px;
            flex-wrap: wrap;
        }
        .navbar .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .navbar .nav-links a i { margin-left: 5px; }
        .navbar .nav-links a:hover { color: #777; }
        .collections-title {
            text-align: center;
            margin-top: 40px;
            font-size: 3em;
            font-weight: bold;
        }
        .sort-dropdown {
            display: flex;
            justify-content: flex-end;
            margin: 20px 20px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            padding: 20px 10px;
        }
        .product-item {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .product-item img {
            width: 100%;
            max-width: 150px;
            height: auto;
            margin: 0 auto;
        }
        .product-name {
            margin-top: 10px;
            font-size: 1em;
            font-weight: bold;
            word-wrap: break-word;
        }
        .product-price {
            margin-top: 5px;
            font-size: 0.95em;
            color: #c0a177;
        }
        .add-to-cart {
            margin-top: 10px;
            background-color: #F26B8A;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px;
            font-size: 0.9em;
            width: 100%;
            cursor: pointer;
        }
        .add-to-cart:hover { background-color: #d55775; }
        @media screen and (max-width: 768px) {
            .navbar { flex-direction: column; align-items: flex-start; }
            .navbar .logo { margin-left: 20px; margin-bottom: 10px; }
            .navbar .nav-links { flex-direction: column; align-items: flex-start; }
            .sort-dropdown { margin: 10px 20px; }
            .collections-title { font-size: 2em; }
        }
        @media screen and (max-width: 480px) {
            .product-name { font-size: 0.9em; }
            .product-price { font-size: 0.85em; }
            .add-to-cart { font-size: 0.85em; padding: 6px; }
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
        
    </style>
</head>
<body>

<div class="container">
    <div class="navbar">
        <div class="logo">Aurum Jewelry PH</div>
        <div class="nav-links">
            <a href="index.php">HOME</a>
            <a href="collections.php">COLLECTIONS</a>
            
            <a href="cart.php" title="Cart"><i class="fas fa-shopping-cart"></i></a>
            <a href="orders.php" title="Orders"><i class="fas fa-box"></i></a>
            <a href="profile.php" title="Profile"><i class="fas fa-user"></i></a>
        </div>
    </div>

    <div class="collections-title">Our Collection</div>

    <div class="sort-dropdown">
        <label for="sort-options">Sort by:</label>
        <select id="sort-options" onchange="sortProducts()">
            <option value="default">Default</option>
            <option value="price-asc">Price Low to High</option>
            <option value="price-desc">Price High to Low</option>
            <option value="name-asc">Name A-Z</option>
            <option value="name-desc">Name Z-A</option>
        </select>
    </div>

    <div class="product-grid" id="product-grid">
       
    </div>
</div>

<script>
    function sortProducts() {
        const grid = document.getElementById('product-grid');
        const items = Array.from(grid.getElementsByClassName('product-item'));
        const sortOption = document.getElementById('sort-options').value;

        items.sort((a, b) => {
            switch (sortOption) {
                case 'price-asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'name-asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name-desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                default:
                    return 0;
            }
        });

        grid.innerHTML = '';
        items.forEach(item => grid.appendChild(item));
    }


  const loggedIn = <?php echo json_encode($loggedIn); ?>;

document.addEventListener("DOMContentLoaded", () => {
    fetch("get_products.php")
        .then(response => response.json())
        .then(products => {
            const grid = document.getElementById('product-grid');

            products.forEach(product => {
                const item = document.createElement('div');
                item.className = 'product-item';
                item.dataset.price = product.price;
                item.dataset.name = product.productName;
                item.innerHTML = `
                    <img src="${product.image}" alt="${product.productName}">
                    <div class="product-name">${product.productName}</div>
                    <div class="product-price">Php ${parseFloat(product.price).toLocaleString()}</div>
                    <button class="add-to-cart" onclick="addToCart(${product.productID}, '${product.productName}')">Add to Cart</button>
                `;
                grid.appendChild(item);
            });
        })
        .catch(error => {
            console.error("Error loading products:", error);
            document.getElementById("product-grid").innerHTML = "<p>Error loading products.</p>";
        });
});


    const productGrid = document.getElementById('product-grid');
    products.forEach(product => {
        const item = document.createElement('div');
        item.className = 'product-item';
        item.dataset.price = product.price;
        item.dataset.name = product.name;
        item.innerHTML = `
            <img src="${product.image}" alt="${product.name}">
            <div class="product-name">${product.name}</div>
            <div class="product-price">Php ${product.price.toLocaleString()}</div>
            <button class="add-to-cart" onclick="addToCart('${product.name}', ${product.price})">Add to Cart</button>
        `;
        productGrid.appendChild(item);
    });
  function addToCart(productID, productName) {
    if (!loggedIn) {
        alert("Please log in to add items to your cart.");
        window.location.href = "login.php";
        return;
    }

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ productID: productID })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${productName} has been added to your cart.`);
        } else {
            alert("Failed to add to cart: " + data.error);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred. Please try again later.");
    });
}
</script>

</body>


<footer class="footer">
    <div class="footer-left">
        <a href="#" id="storePolicyLink">Store Policy</a><br>
        <a href="#" id="paymentLink">Mode of Payment</a>
    </div>
    <div class="footer-center">
        <p>Email: <a href="mailto:aurumjewelryph@gmail.com">aurumjewelryph@gmail.com</a></p>
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
