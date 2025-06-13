<?php
session_start();

if (!isset($_SESSION["userID"])) {
    die("User not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "u663344503_221024", "Database_3", "u663344503_users");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userID = $_SESSION["userID"];

    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $contact = $conn->real_escape_string($_POST["contact"]);
    $address = $conn->real_escape_string($_POST["address"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["profile"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;

    if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFile)) {

        $stmt = $conn->prepare("UPDATE user_info SET name = ?, email = ?, contact = ?, address = ?, profile = ?, password = ? WHERE userID = ?");
        $stmt->bind_param("ssssssi", $name, $email, $contact, $address, $targetFile, $password, $userID);

        if ($stmt->execute()) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        } else {
            echo "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading profile photo.";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
    <style>
       
        body { 
            font-family: Arial, sans-serif; 
            background-color: #FDA4BA; 
            padding: 50px;
        }
        header {
             font-family: didot; 
             background-color: #FDA4BA;
             color: white;
             text-align: center;
            padding: 30px 0 10px 0;
        }
        header h1 {
        margin: 0;
        font-size: 2em;
         }

   
        .container {
            max-width: 800px; 
            margin: auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(190, 170, 170, 0.1); 
            margin-top: 30px; 
            display: flex; 
        }

        .left-section {
            font-family: didot; 
            flex: 1; 
            padding: 100px;
            border-right: 1px solid #ddd; 
            text-align: center;
        }

        .right-section {
            flex: 2; 
            padding: 20px;
            margin-top: 30px;
        }

        input { 
            width: 100%; 
            padding: 10px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }

        button { 
            width: 100%; 
            padding: 10px; 
            background-color: #F26B8A; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin-top: 10px; 
        }
        
        button:hover { 
            background-color: #ff8da1; 
        }

        p { 
            text-align: center; 
        }

        .logo { 
            display: block;  
            margin: 0 auto 20px auto;
            max-width: 175px; 
            height: 175px; 
            border-radius: 50%; 
            object-fit: cover; 
        }

    </style>
<body>
    <div class="container">
    <div class="left-section">
        <h2>Welcome to Aurum Jewelry PH!</h2>
        <p>If you don't want to edit your profile.</p>
        <button onclick="window.location.href='profile.php';">Back to Profile</button>
    </div>
     <div class="container">
    <div class="form-box">
    <h2>Edit Profile</h2>
    <form action="update_profile.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" placeholder="Your full name" required /><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Your email address" required /><br><br>

        <label for="address">Complete Address:</label>
        <input type="text" name="address" placeholder="Your complete address" required /><br><br>

        <label for="contact">Contact Number:</label>
        <input type="text" name="contact" placeholder="Your contact number" required pattern="[0-9+]{10,15}" title="Enter a valid contact number" /><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Your password" required /><br><br>

        <label for="profile">Upload Profile Photo:</label>
        <input type="file" name="profile" accept="image/*" required /><br><br>

        <button type="submit" name="update">Save Changes</button>
    </form>

</body>
</html>
